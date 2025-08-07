<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/Strategies/DeliveryStrategy.php';
require_once __DIR__ . '/../app/Strategies/Delivery/GrabDelivery.php';
require_once __DIR__ . '/../app/Strategies/Delivery/SelfPickup.php';
require_once __DIR__ . '/../app/SecurityLogger.php';

use App\SecurityLogger;

if (!defined('BASE_URL')) {
    define('BASE_URL', '/Assignment');
}

class OrderController
{
    private SecurityLogger $logger;

    public function __construct()
    {
        $this->logger = new SecurityLogger(Database::getInstance());
    }

    // Accept $type as a parameter instead of reading $_GET inside
    public function confirm(string $type = 'pickup')
    {
        Session::start();

        $orderModel = new Order();

        // Get cart from session
        $cart = $_SESSION['cart'] ?? [];

        // Redirect to cart page if cart is empty
        if (empty($cart)) {
            header('Location: ' . BASE_URL . '/routes/cart.php');
            exit;
        }

        // Step 1: Apply the delivery strategy based on the $type
        $deliveryStrategy = match ($type) {
            'pickup' => new SelfPickup(),
            'delivery' => new GrabDelivery(),
            default => throw new Exception("Invalid order type: $type"),
        };

        // Calculate fees and notes
        $subtotal = $orderModel->calculateSubtotal($cart); // You should have this method in Order model
        $deliveryFee = $deliveryStrategy->getFee();
        $deliveryNote = $deliveryStrategy->getMessage();
        $total = $subtotal + $deliveryFee; // Needed for view display


        // Step 2: Get branches only if pickup
        $branches = [];
        if ($type === 'pickup') {
            $branches = $orderModel->getBranches();
        }

        // Step 3: Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? null;
            $phone = trim($_POST['phone'] ?? '');
            $address = $type === 'delivery' ? trim($_POST['address'] ?? '') : '';
            $branch_id = $type === 'pickup' ? ($_POST['branch_id'] ?? null) : null;

            // Calculate total from cart items
            $subtotal = $orderModel->calculateSubtotal($cart);
            $total = $subtotal + $deliveryFee;

            // Save order info to session (pending order)
            $orderModel->savePendingOrder([
                'user_id'   => $user_id,
                'phone'     => $phone,
                'address'   => $address,
                'type'      => $type,
                'branch_id' => $branch_id,
                'delivery_fee' => $deliveryFee,
                'total'     => $total,
            ]);

            // Log order confirmation
            $this->logger->logEvent('INFO', 'ORDER_CONFIRM', [
                'user_id' => $user_id,
                'order_type' => $type,
                'amount' => $total,
                'branch_id' => $branch_id,
                'delivery_fee' => $deliveryFee,
                'items_count' => count($cart)
            ]);

            // Redirect to payment page
            header('Location: ' . BASE_URL . '/routes/payment.php');
            exit;
        }

        // Load view with relevant variables
        require_once __DIR__ . '/../views/order/confirm_order.php';
    }
}
