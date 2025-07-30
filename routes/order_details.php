<?php
require_once __DIR__ . '/../controllers/OrderDetailController.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $controller = new OrderDetailController();
    $controller->show($id);
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid order ID.</div></div>";
}