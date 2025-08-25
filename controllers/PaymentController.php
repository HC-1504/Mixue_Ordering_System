<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/SecurityLogger.php';

use App\SecurityLogger;

class PaymentController
{
    private SecurityLogger $logger;

    public function __construct()
    {
        $this->logger = new SecurityLogger(Database::getInstance());
    }

    public function show()
    {
        Session::start();

        if (!isset($_SESSION['pending_order'])) {
            header('Location: ' . BASE_URL . '/views/menu.php');
            exit;
        }

        $order = $_SESSION['pending_order'];
        $userModel = new User();
        $user_balance = $userModel->getBalance($order['user_id']);
        $payment_success = false;
        $error = '';

        require_once __DIR__ . '/../views/payment/confirm_payment.php';
    }

    public function process()
    {
        Session::start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['pending_order'])) {
            echo json_encode(['error' => 'No pending order found.']);
            exit;
        }

        $orderData = $_SESSION['pending_order'];
        $orderData['status'] = 'pending'; // Set initial status

        $orderModel = new Order();
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            echo json_encode(['error' => 'Your cart is empty.']);
            exit;
        }

        $orderId = $orderModel->createOrder($orderData);

        if ($orderId) {
            // Save order details immediately
            $orderModel->addDetails($orderId, $cart);
            $_SESSION['pending_order_id'] = $orderId;
            echo json_encode(['success' => true, 'orderId' => $orderId]);
        } else {
            echo json_encode(['error' => 'Failed to create a pending order.']);
        }
    }

    public function finalizeOrder($orderId, $paymentMethod)
    {
        $orderModel = new Order();
        $order = $orderModel->getOrderById($orderId);

        if (!$order) {
            $this->logger->logEvent('CRITICAL', 'FINALIZE_ORDER_FAIL', ['reason' => 'Order not found', 'order_id' => $orderId]);
            return;
        }

        $paymentModel = new Payment();
        $conn = Database::getInstance();

        try {
            $conn->beginTransaction();

            // Record payment
            $paymentModel->record($orderId, $order['total'], $paymentMethod);
            $this->logger->logEvent('INFO', 'PAYMENT_SUCCESS', [
                'user_id' => $order['user_id'],
                'order_id' => $orderId,
                'amount' => $order['total'],
                'payment_method' => $paymentMethod
            ]);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            $this->logger->logEvent('CRITICAL', 'FINALIZE_ORDER_FAIL', [
                'user_id' => $order['user_id'],
                'reason' => 'Transaction failed',
                'error_message' => $e->getMessage(),
                'order_id' => $orderId
            ]);
        }
    }

    public function clientSideFinalizePayment()
    {
        require_once __DIR__ . '/../vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $paymentIntentId = $data['paymentIntentId'] ?? null;

        if (!$paymentIntentId) {
            echo json_encode(['success' => false, 'error' => 'No Payment Intent ID provided.']);
            return;
        }

        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            $metadata = $paymentIntent->metadata;
            $userId = $metadata->user_id ?? null;
            $orderId = $metadata->order_id ?? null;
            $amount = $paymentIntent->amount / 100; // Amount from Stripe is in cents

            if (!$userId || !$orderId) {
                echo json_encode(['success' => false, 'error' => 'Missing user_id or order_id in Payment Intent metadata.']);
                return;
            }

            $userModel = new User();
            $orderModel = new Order();
            $paymentModel = new Payment();

            // Check sufficient balance
            $currentBalance = $userModel->getBalance($userId);
            if ($currentBalance < $amount) {
                $orderModel->updateStatus($orderId, 'Cancelled');
                echo json_encode(['success' => false, 'error' => 'Insufficient balance.']);
                $this->logger->logEvent('WARN', 'CLIENT_SIDE_FINALIZATION_FAIL', ['reason' => 'Insufficient balance', 'order_id' => $orderId, 'payment_intent_id' => $paymentIntentId]);
                return;
            }

            // Deduct balance from user account
            $deductionResult = $userModel->deductBalance($userId, $amount);

            if ($deductionResult !== true) {
                $orderModel->updateStatus($orderId, 'Cancelled');
                $errorMessage = 'Failed to deduct user balance.';
                echo json_encode(['success' => false, 'error' => $errorMessage]);
                $this->logger->logEvent('WARN', 'CLIENT_SIDE_FINALIZATION_FAIL', ['reason' => $errorMessage, 'user_id' => $userId, 'amount' => $amount, 'payment_intent_id' => $paymentIntentId]);
                return;
            }

            // Get payment method type from the associated Charge object
            $paymentMethodType = 'Others'; // Default value
            if ($paymentIntent->latest_charge) {
                try {
                    $charge = \Stripe\Charge::retrieve($paymentIntent->latest_charge);
                    $paymentMethodType = $charge->payment_method_details->type ?? 'Others';
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->logger->logEvent('WARN', 'STRIPE_CHARGE_RETRIEVAL_ERROR', ['message' => $e->getMessage(), 'payment_intent_id' => $paymentIntentId, 'charge_id' => $paymentIntent->latest_charge]);
                }
            }

            // Record payment
            $paymentRecorded = $paymentModel->record($orderId, $amount, $paymentMethodType);

            if (!$paymentRecorded) {
                // This is a critical issue. The balance was deducted, but payment was not recorded.
                // We need to refund the user and log this as a critical error.
                $userModel->addBalance($userId, $amount); // Refund user
                $orderModel->updateStatus($orderId, 'Cancelled');
                echo json_encode(['success' => false, 'error' => 'Failed to record payment. Your balance has been restored.']);
                $this->logger->logEvent('CRITICAL', 'PAYMENT_RECORDING_FAIL_AFTER_DEDUCTION', [
                    'reason' => 'Failed to record payment after balance deduction. User refunded.',
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'payment_intent_id' => $paymentIntentId
                ]);
                return;
            }

            // Finalize order
            $orderModel->updateStatus($orderId, 'Pending');

            $this->logger->logEvent('INFO', 'CLIENT_SIDE_FINALIZATION_SUCCESS', [
                'user_id' => $userId,
                'order_id' => $orderId,
                'amount' => $amount,
                'payment_intent_id' => $paymentIntentId,
                'payment_method' => $paymentMethodType
            ]);

            echo json_encode(['success' => true]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            $this->logger->logEvent('WARN', 'STRIPE_API_ERROR', ['message' => $e->getMessage(), 'payment_intent_id' => $paymentIntentId]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'An unexpected error occurred: ' . $e->getMessage()]);
            $this->logger->logEvent('CRITICAL', 'UNEXPECTED_ERROR', ['message' => $e->getMessage(), 'payment_intent_id' => $paymentIntentId]);
        }
    }
}
