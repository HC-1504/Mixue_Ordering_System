<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../controllers/PaymentController.php';

header('Content-Type: application/json');

$controller = new PaymentController();
$controller->clientSideFinalizePayment();
