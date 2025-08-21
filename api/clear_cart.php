<?php
require_once __DIR__ . '/../includes/session.php';

Session::start();

unset($_SESSION['cart']);
unset($_SESSION['pending_order']);
unset($_SESSION['pending_order_id']);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Cart cleared.']);
