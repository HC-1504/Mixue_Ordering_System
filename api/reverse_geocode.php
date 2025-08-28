<?php
header('Content-Type: application/json');

$lat = $_GET['latitude'] ?? null;
$lon = $_GET['longitude'] ?? null;

if ($lat === null || $lon === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Latitude and longitude are required.']);
    exit;
}

// Nominatim API endpoint
$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}";

// Create a stream context to set a custom User-Agent (required by Nominatim's policy)
$opts = [
    'http' => [
        'method' => "GET",
        'header' => "User-Agent: Mixue-PHP-App/1.0\r\n"
    ]
];
$context = stream_context_create($opts);

// Make the API call
$response = file_get_contents($url, false, $context);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to contact the location service.']);
    exit;
}

$data = json_decode($response, true);

if (isset($data['error'])) {
    http_response_code(404);
    echo json_encode(['error' => $data['error']]);
} else {
    echo json_encode([
        'address' => $data['display_name'] ?? 'Address not found.'
    ]);
}

