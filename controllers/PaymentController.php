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
            $amount = $paymentIntent->amount_received / 100; // Amount in dollars

            if ($paymentIntent->status !== 'succeeded') {
                echo json_encode(['success' => false, 'error' => 'Payment Intent status is not succeeded.']);
                return;
            }

            if (!$userId || !$orderId) {
                echo json_encode(['success' => false, 'error' => 'Missing user_id or order_id in Payment Intent metadata.']);
                return;
            }

            // Get payment method type from the associated Charge object
            $paymentMethodType = 'unknown'; // Default value
            if ($paymentIntent->latest_charge) {
                try {
                    $charge = \Stripe\Charge::retrieve($paymentIntent->latest_charge);
                    $paymentMethodType = $charge->payment_method_details->type ?? 'unknown';
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->logger->logEvent('WARN', 'STRIPE_CHARGE_RETRIEVAL_ERROR', ['message' => $e->getMessage(), 'payment_intent_id' => $paymentIntentId, 'charge_id' => $paymentIntent->latest_charge]);
                }
            }

            // Record payment
            $paymentModel = new Payment();
            $paymentRecorded = $paymentModel->record($orderId, $amount, $paymentMethodType);

            if (!$paymentRecorded) {
                // Update order status to 'Cancelled' due to payment failure
                $orderModel = new Order();
                $orderModel->updateStatus($orderId, 'Cancelled');

                echo json_encode(['success' => false, 'error' => 'Failed to record payment.']);
                $this->logger->logEvent('WARN', 'CLIENT_SIDE_FINALIZATION_FAIL', ['reason' => 'Failed to record payment', 'order_id' => $orderId, 'payment_intent_id' => $paymentIntentId]);
                return;
            }

            // Deduct user balance
            $userModel = new User();
            $deductionResult = $userModel->deductBalance($userId, $amount);

            if ($deductionResult !== 'success') {
                // Update order status to 'Cancelled' due to payment failure
                $orderModel = new Order();
                $orderModel->updateStatus($orderId, 'Cancelled');

                $errorMessage = 'Failed to deduct user balance.';
                if ($deductionResult === 'insufficient_balance') {
                    $errorMessage = 'Insufficient balance.';
                } elseif ($deductionResult === 'user_not_found') {
                    $errorMessage = 'User not found for balance deduction.';
                } elseif ($deductionResult === 'db_error') {
                    $errorMessage = 'Database error during balance deduction.';
                }
                echo json_encode(['success' => false, 'error' => $errorMessage]);
                $this->logger->logEvent('WARN', 'CLIENT_SIDE_FINALIZATION_FAIL', ['reason' => $errorMessage, 'user_id' => $userId, 'amount' => $amount, 'payment_intent_id' => $paymentIntentId]);
                return;
            }

            $this->logger->logEvent('INFO', 'CLIENT_SIDE_FINALIZATION_SUCCESS', [
                'user_id' => $userId,
                'order_id' => $orderId,
                'amount' => $amount,
                'payment_intent_id' => $paymentIntentId
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
