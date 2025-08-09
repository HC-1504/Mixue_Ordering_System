<?php
require_once __DIR__ . '/../models/OrderDetail.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../includes/session.php'; // Ensure session is available

class OrderDetailController
{
    public function show($id)
    {
        Session::start(); // Start the session to access user ID

        // 1. Check if user is logged in. If not, they can't own any orders.
        if (!isset($_SESSION['user_id'])) {
            // Or redirect to login page
            die('Access Denied: You must be logged in to view orders.');
        }

        $orderModel = new Order();
        $detailModel = new OrderDetail();

        $order = $orderModel->find($id);

        // 2. CRITICAL IDOR CHECK:
        // Verify the order exists AND that the 'user_id' of the order
        // matches the user ID stored in the session.
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            // Use a generic not found page to avoid leaking information
            // that the order exists but belongs to someone else.
            require __DIR__ . '/../views/order/not_found.php';
            return;
        }

        $details = $detailModel->getOrderDetailsByOrderId($id);
        require __DIR__ . '/../views/order/order_details.php';
    }
}
