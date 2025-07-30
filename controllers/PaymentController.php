<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';

class PaymentController
{
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

        if (empty($payment_type)) {
            $error = 'Please select a payment method.';
        } elseif ($user_balance < $order['total']) {
            $error = 'Insufficient balance. Please reload your wallet.';
        } else {
            try {
                $conn->beginTransaction();

                // Deduct user balance
                $userModel->deductBalance($order['user_id'], $order['total']);

                // Save order                
                $orderId = $orderModel->createOrder($order);
                if (!$orderId) {
                    throw new Exception('Failed to create order.');
                }

                // Save order details
                $orderModel->addDetails($orderId, $cart);

                // Record payment
                $paymentModel->record($orderId, $order['total'], $payment_type);

                $conn->commit();

                unset($_SESSION['cart'], $_SESSION['pending_order']);
                $payment_success = true;
                $user_balance = $userModel->getBalance($order['user_id']); // refresh balance
            } catch (Exception $e) {
                $conn->rollBack();
                $error = 'Payment failed: ' . $e->getMessage();
            }
        }

        require_once __DIR__ . '/../views/payment/confirm_payment.php';
    }
}
