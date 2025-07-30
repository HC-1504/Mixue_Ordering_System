<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/auth.php';

Session::start();
if (!Session::isLoggedIn() || Session::get('user_role') !== 'manager') {
    header("Location: ../views/login_logout_modules/login.php");
    exit();
}

require_once __DIR__ . '/../controllers/admin/ReportController.php';

$controller = new ReportController();
$controller->index(); 