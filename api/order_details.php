<?php
require_once __DIR__ . '/../controllers/OrderDetailController.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required.']);
    exit;
}

$controller = new OrderDetailController();
$controller->getOrderDetailsApi($id);
