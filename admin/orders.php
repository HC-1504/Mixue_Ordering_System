<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/auth.php';

Session::start();
if (!Session::isLoggedIn() || !in_array(Session::get('user_role'), ['admin', 'manager'])) {
    header("Location: ../views/login_logout_modules/login.php");
    exit();
}

require_once __DIR__ . '/../controllers/admin/OrderController.php';

$controller = new OrderController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'view':
        if ($id) {
            $controller->view($id);
        } else {
            $controller->index();
        }
        break;
    case 'update_status':
        $controller->updateStatus();
        break;
    default:
        $controller->index();
        break;
} 