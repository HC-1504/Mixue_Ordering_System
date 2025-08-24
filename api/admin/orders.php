<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/OrderDetail.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Start session and check admin permissions
Session::start();

// Check if user is logged in and has admin/manager role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'manager'])) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Access Denied: Admin privileges required'
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$orderModel = new Order();
$orderDetailModel = new OrderDetail();

try {
    switch ($method) {
        case 'GET':
            handleGetRequest();
            break;
        case 'PUT':
            handlePutRequest();
            break;
        default:
            http_response_code(405);
            echo json_encode([
                'status' => 'error',
                'message' => 'Method not allowed'
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}

function handleGetRequest() {
    global $orderModel, $orderDetailModel;
    
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    $lastSegment = end($pathParts);
    
    // Check if requesting specific order
    if (is_numeric($lastSegment)) {
        $orderId = (int)$lastSegment;
        $order = $orderModel->getOrderById($orderId);
        
        if (!$order) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Order not found'
            ]);
            return;
        }
        
        // Get order details
        $orderDetails = $orderDetailModel->getOrderDetailsByOrderId($orderId);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'order' => $order,
                'details' => $orderDetails
            ]
        ]);
    } elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $orderId = (int)$_GET['id'];
        $order = $orderModel->getOrderById($orderId);
        
        if (!$order) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Order not found'
            ]);
            return;
        }
        
        // Get order details
        $orderDetails = $orderDetailModel->getOrderDetailsByOrderId($orderId);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'order' => $order,
                'details' => $orderDetails
            ]
        ]);
    } else {
        // Get all orders with filters
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $limit = (int)($_GET['limit'] ?? 50);
        $offset = (int)($_GET['offset'] ?? 0);
        
        $orders = $orderModel->getAllOrdersWithFilters($search, $status, $limit, $offset);
        $total = $orderModel->getOrdersCount($search, $status);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'orders' => $orders,
                'pagination' => [
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $total
                ]
            ]
        ]);
    }
}

function handlePutRequest() {
    global $orderModel;
    
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    // Find numeric id position (works for orders or orders.php)
    $orderId = null;
    $statusKeywordIndex = null;
    foreach ($pathParts as $index => $segment) {
        if (is_numeric($segment)) {
            $orderId = (int)$segment;
            $statusKeywordIndex = $index + 1;
            break;
        }
    }
    
    // Check if updating order status
    if ($orderId !== null && isset($pathParts[$statusKeywordIndex]) && $pathParts[$statusKeywordIndex] === 'status') {
        
        // Get PUT data
        $putData = file_get_contents('php://input');
        $data = json_decode($putData, true);
        
        if (!isset($data['status'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Status field is required'
            ]);
            return;
        }
        
        $newStatus = $data['status'];
        $allowedStatuses = ['Pending', 'Preparing', 'Out for Delivery', 'Completed', 'Cancelled'];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid status value'
            ]);
            return;
        }
        
        $result = $orderModel->updateStatus($orderId, $newStatus);
        
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => "Order #{$orderId} status updated to {$newStatus}",
                'data' => [
                    'order_id' => $orderId,
                    'new_status' => $newStatus
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update order status'
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid endpoint'
        ]);
    }
}
