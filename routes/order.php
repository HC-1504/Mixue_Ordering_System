<?php
require_once __DIR__ . '/../controllers/OrderController.php';

$action = $_GET['action'] ?? 'confirm';
$type = $_GET['type'] ?? null;

$controller = new OrderController();

if ($action === 'confirm' && $type !== null) {
    $controller->confirm($type);
} else {
    // redirect to cart if action/type missing or invalid
    header('Location: ' . BASE_URL . '/routes/cart.php');
    exit;
}
