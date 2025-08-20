<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/auth.php';

Session::start();
if (!Session::isLoggedIn() || !in_array(Session::get('user_role'), ['admin', 'manager'])) {
    header("Location: ../views/login_logout_modules/login.php");
    exit();
}

require_once __DIR__ . '/../controllers/admin/ReportController.php';

$controller = new ReportController();

// AJAX endpoint for generating the business report
if (isset($_GET['action']) && $_GET['action'] === 'generate_report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get POST data for date filtering
    $postData = json_decode(file_get_contents('php://input'), true);
    $startDate = $postData['start_date'] ?? null;
    $endDate = $postData['end_date'] ?? null;
    
    $result = $controller->generateBusinessReport($startDate, $endDate);
    echo json_encode($result);
    exit();
}

// Default: render the reports dashboard page
$controller->index();