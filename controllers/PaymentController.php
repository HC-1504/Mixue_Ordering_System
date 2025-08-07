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

        if (!isset($_SESSION['pending_order'])) {
            header('Location: ' . BASE_URL . '/views/menu.php');
            exit;
        }

        $order = $_SESSION['pending_order'];
        $cart = $_SESSION['cart'] ?? [];

        $userModel = new User();
        $orderModel = new Order();
        $paymentModel = new Payment();
        $conn = Database::getInstance();

        $user_balance = $userModel->getBalance($order['user_id']);
        $payment_type = $_POST['payment_type'] ?? '';

        $payment_success = false;
        $error = '';

        // Log payment attempt
        $this->logger->logEvent('INFO', 'PAYMENT_ATTEMPT', [
            'user_id' => $order['user_id'],
            'amount' => $order['total'],
            'payment_method' => $payment_type,
            'order_type' => $order['type'],
            'branch_id' => $order['branch_id'] ?? null
        ]);

        if (empty($payment_type)) {
            $error = 'Please select a payment method.';
            $this->logger->logEvent('WARN', 'PAYMENT_FAIL', [
                'user_id' => $order['user_id'],
                'reason' => 'No payment method selected',
                'amount' => $order['total']
            ]);
        } elseif ($user_balance < $order['total']) {
            $error = 'Insufficient balance. Please reload your wallet.';
            $this->logger->logEvent('WARN', 'PAYMENT_FAIL', [
                'user_id' => $order['user_id'],
                'reason' => 'Insufficient balance',
                'amount' => $order['total'],
                'user_balance' => $user_balance
            ]);
        } else {
            try {
                $conn->beginTransaction();

                // Deduct user balance
                $userModel->deductBalance($order['user_id'], $order['total']);
                $this->logger->logEvent('INFO', 'BALANCE_DEDUCT_SUCCESS', [
                    'user_id' => $order['user_id'],
                    'amount' => $order['total'],
                    'previous_balance' => $user_balance,
                    'new_balance' => $user_balance - $order['total']
                ]);

                // Save order
                $orderId = $orderModel->createOrder($order);
                if (!$orderId) {
                    throw new Exception('Failed to create order.');
                }

                $this->logger->logEvent('INFO', 'ORDER_CREATE_SUCCESS', [
                    'user_id' => $order['user_id'],
                    'order_id' => $orderId,
                    'amount' => $order['total'],
                    'order_type' => $order['type'],
                    'branch_id' => $order['branch_id'] ?? null,
                    'delivery_fee' => $order['delivery_fee'] ?? 0
                ]);

                // Save order details
                $orderModel->addDetails($orderId, $cart);
                $this->logger->logEvent('INFO', 'ORDER_DETAILS_ADDED', [
                    'user_id' => $order['user_id'],
                    'order_id' => $orderId,
                    'items_count' => count($cart)
                ]);

                // Record payment
                $paymentModel->record($orderId, $order['total'], $payment_type);
                $this->logger->logEvent('INFO', 'PAYMENT_SUCCESS', [
                    'user_id' => $order['user_id'],
                    'order_id' => $orderId,
                    'amount' => $order['total'],
                    'payment_method' => $payment_type
                ]);

                $conn->commit();

                unset($_SESSION['cart'], $_SESSION['pending_order']);
                $payment_success = true;
                $user_balance = $userModel->getBalance($order['user_id']); // refresh balance
            } catch (Exception $e) {
                $conn->rollBack();
                $error = 'Payment failed: ' . $e->getMessage();

                $this->logger->logEvent('CRITICAL', 'PAYMENT_FAIL', [
                    'user_id' => $order['user_id'],
                    'reason' => 'Transaction failed',
                    'error_message' => $e->getMessage(),
                    'amount' => $order['total'],
                    'payment_method' => $payment_type
                ]);
            }
        }

        require_once __DIR__ . '/../views/payment/confirm_payment.php';
    }
}
