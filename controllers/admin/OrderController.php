<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/session.php';

class OrderController {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    public function index() {
        // Admin和Manager都可以查看订单
        if (!in_array(Session::get('user_role'), ['admin', 'manager'])) {
            Session::set('order_errors', ['You do not have permission to access order management.']);
            header('Location: ../admin/dashboard.php');
            exit();
        }
        
        $orders = $this->getAllOrders();
        $success = Session::get('order_success');
        Session::unset('order_success');
        $errors = Session::get('order_errors');
        Session::unset('order_errors');
        
        $page_title = 'Order Management';
        require_once __DIR__ . '/../../views/admin/orders/index.php';
    }
    
    public function updateStatus() {
        // Admin和Manager都可以更新订单状态
        if (!in_array(Session::get('user_role'), ['admin', 'manager'])) {
            Session::set('order_errors', ['You do not have permission to update order status.']);
            header('Location: orders.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::set('order_errors', ['Invalid request method.']);
            header('Location: orders.php');
            exit();
        }
        
        if (!Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
            Session::set('order_errors', ['Invalid CSRF token.']);
            header('Location: orders.php');
            exit();
        }
        
        $orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        $newStatus = trim($_POST['new_status'] ?? '');
        
        $allowedStatuses = ['Pending', 'Preparing', 'Out for Delivery', 'Completed', 'Cancelled'];
        if (!$orderId || !in_array($newStatus, $allowedStatuses)) {
            Session::set('order_errors', ['Invalid order ID or status.']);
            header('Location: orders.php');
            exit();
        }
        
        try {
            $result = $this->updateOrderStatus($orderId, $newStatus);
            if ($result) {
                Session::set('order_success', "Order #{$orderId} status updated to {$newStatus}.");
            } else {
                Session::set('order_errors', ['Failed to update order status.']);
            }
        } catch (Exception $e) {
            Session::set('order_errors', ['An error occurred while updating the order status.']);
            error_log('Order status update error: ' . $e->getMessage());
        }
        
        header('Location: orders.php');
        exit();
    }
    
    public function view($orderId) {
        // Admin和Manager都可以查看订单详情
        if (!in_array(Session::get('user_role'), ['admin', 'manager'])) {
            Session::set('order_errors', ['You do not have permission to view order details.']);
            header('Location: orders.php');
            exit();
        }
        
        $order = $this->getOrderById($orderId);
        if (!$order) {
            Session::set('order_errors', ['Order not found.']);
            header('Location: orders.php');
            exit();
        }
        
        $orderDetails = $this->getOrderDetails($orderId);
        $page_title = "Order #{$orderId} Details";
        require_once __DIR__ . '/../../views/admin/orders/view.php';
    }
    
    private function getAllOrders() {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email,
                   b.name as branch_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN branches b ON o.branch_id = b.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getOrderById($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email,
                   b.name as branch_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN branches b ON o.branch_id = b.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
    
    private function getOrderDetails($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT od.*, p.name as product_name, p.price
            FROM order_details od
            JOIN products p ON od.product_id = p.id
            WHERE od.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    private function updateOrderStatus($orderId, $newStatus) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $orderId]);
    }
} 