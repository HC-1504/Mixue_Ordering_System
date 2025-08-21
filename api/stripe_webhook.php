<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../controllers/PaymentController.php';
require_once __DIR__ . '/../app/SecurityLogger.php';

use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\SecurityLogger;

Stripe::setApiKey(STRIPE_SECRET_KEY);

// You can find your endpoint's secret in your webhook settings
$endpoint_secret = 'whsec_...'; // You'll need to get this from your Stripe dashboard

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

$logger = new SecurityLogger(Database::getInstance());

// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object;
        $metadata = $paymentIntent->metadata;
        $userId = $metadata->user_id;
        $purpose = $metadata->purpose;
        $amount = $paymentIntent->amount_received / 100;

        if ($purpose === 'order') {
            $orderId = $metadata->order_id;
            if ($orderId) {
                $paymentController = new PaymentController();
                $paymentController->finalizeOrder($orderId, 'stripe');
                $logger->logEvent('INFO', 'STRIPE_ORDER_SUCCESS', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent->id
                ]);
            } else {
                $logger->logEvent('CRITICAL', 'STRIPE_ORDER_FAIL', [
                    'reason' => 'Missing order_id in metadata',
                    'payment_intent_id' => $paymentIntent->id
                ]);
            }
        }
        break;
    default:
        // Unexpected event type
        http_response_code(400);
        exit();
}

http_response_code(200);
