<?php
require_once __DIR__ . '/controllers/ReloadController.php';

$controller = new ReloadController();

// Basic routing
if (isset($_GET['payment_intent'])) {
    $controller->handleStripeReturn();
} else {
    $controller->showReloadPage();
}
