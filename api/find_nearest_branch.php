<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../models/Branch.php';

$lat = $_GET['latitude'] ?? null;
$lon = $_GET['longitude'] ?? null;

if ($lat === null || $lon === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Latitude and longitude are required.']);
    exit;
}

try {
    $branchModel = new Branch();
    $nearestBranch = $branchModel->findNearestBranch(floatval($lat), floatval($lon));

    if ($nearestBranch) {
        echo json_encode($nearestBranch);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'No branches with coordinates found.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An internal error occurred: ' . $e->getMessage()]);
}
