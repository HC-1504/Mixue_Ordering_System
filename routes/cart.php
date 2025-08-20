<?php
require_once __DIR__ . '/../controllers/CartController.php';

$controller = new CartController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $controller->add();
            break;
        case 'add_batch':
            $controller->addBatch();
            break;
        case 'update':
            $controller->update($_POST);
            break;
        case 'remove':
            $controller->remove($_POST['index']);
            break;
        case 'remove_all':
            $controller->removeAll();
            break;
        default:
            $controller->index();
            break;
    }
} else {
    $controller->index();
}
