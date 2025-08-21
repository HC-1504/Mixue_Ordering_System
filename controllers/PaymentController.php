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

            // As requested, we will not update the order status. It will remain 'pending'.

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
}
