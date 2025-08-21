<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

header('Content-Type: application/json');

Session::start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$amount = $input['amount'] ?? 0;
$purpose = $input['purpose'] ?? 'generic'; // 'order' or 'reload'
$orderId = $_SESSION['pending_order_id'] ?? null;

if ($purpose === 'order' && !$orderId) {
    echo json_encode(['error' => 'No pending order ID found in session.']);
    exit;
}

// Enforce a minimum amount (e.g., 2.00 MYR for card payments)
$minimumAmount = 2.00;
if ($amount < $minimumAmount) {
    echo json_encode(['error' => 'Amount must be at least RM ' . number_format($minimumAmount, 2) . '.']);
    exit;
}

try {
    // Create a PaymentIntent with the order amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount * 100, // Amount in cents
        'currency' => 'myr',
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
        'metadata' => [
            'user_id' => $_SESSION['user_id'],
            'purpose' => $purpose,
            'order_id' => $orderId
        ]
    ]);

    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
    ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected server error occurred: ' . $e->getMessage()]);
}
