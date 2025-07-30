<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/auth.php';

Session::start();
if (!Session::isLoggedIn() || !in_array(Session::get('user_role'), ['admin', 'manager'])) {
    header("Location: ../views/login_logout_modules/login.php");
    exit();
}

require_once __DIR__ . '/../controllers/admin/UserController.php';

$controller = new UserController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'change_role':
        $controller->changeRole();
        break;
    default:
        $controller->index();
        break;
} 