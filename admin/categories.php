<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/auth.php';

Session::start();
if (!Session::isLoggedIn() || !in_array(Session::get('user_role'), ['admin', 'manager'])) {
    // Redirect to the main login page, not the admin folder one.
    header("Location: ../views/login_logout_modules/login.php");
    exit();
}

require_once __DIR__ . '/../controllers/admin/CategoryController.php';

$controller = new CategoryController();
// The index method in the controller is designed to handle all actions
// (list, create, update, delete) by checking $_POST and $_GET.
$controller->index();