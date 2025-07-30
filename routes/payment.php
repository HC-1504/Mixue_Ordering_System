<?php
require_once __DIR__ . '/../controllers/PaymentController.php';

$controller = new PaymentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->process();
} else {
    $controller->show();
}
