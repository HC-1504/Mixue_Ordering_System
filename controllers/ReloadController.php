<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Reload.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

class ReloadController
{
    private $userModel;
    private $reloadModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->reloadModel = new Reload();
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    }

    /**
     * Display the reload page, potentially with a status message from a redirect.
     */
    public function showReloadPage()
    {
        Session::start();
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
            exit();
        }

        $userId = Session::get('user_id');
        $user = $this->userModel->find($userId);

        // Pass user data to the view
        require __DIR__ . '/../views/reload.php';
    }

    /**
     * Handle the return from Stripe after a payment attempt.
     */
    public function handleStripeReturn()
    {
        Session::start();
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php?error_message=Session expired');
            exit();
        }

        $userId = Session::get('user_id');
        $paymentIntentId = $_GET['payment_intent'] ?? null;
        $amount = $_GET['amount'] ?? 0; // Amount passed from the initial form

        if (!$paymentIntentId || $amount <= 0) {
            header('Location: ' . BASE_URL . '/reload.php?error_message=Invalid payment details.');
            exit;
        }

        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status == 'succeeded') {
                // Verify the amount to prevent manipulation
                if (($paymentIntent->amount / 100) != $amount) {
                    header('Location: ' . BASE_URL . '/reload.php?error_message=Invalid amount detected.');
                    exit;
                }

                // Get payment method type from the associated Charge object
                $paymentMethodType = 'unknown'; // Default value
                if ($paymentIntent->latest_charge) {
                    try {
                        $charge = \Stripe\Charge::retrieve($paymentIntent->latest_charge);
                        $paymentMethodType = $charge->payment_method_details->type ?? 'unknown';
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        error_log('Stripe API Error retrieving charge for reload: ' . $e->getMessage());
                    }
                }

                // Update user balance
                $this->userModel->updateBalance($userId, $amount);
                $this->reloadModel->addReload($userId, $amount, $paymentMethodType);

                // Redirect with success message
                header('Location: ' . BASE_URL . '/reload.php?success_message=Reload successful!');
                exit;
            } else {
                // Redirect with error message
                header('Location: ' . BASE_URL . '/reload.php?error_message=Payment was not successful.');
                exit;
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Log the error for debugging
            error_log('Stripe API Error: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '/reload.php?error_message=An error occurred with our payment provider.');
            exit;
        }
    }
}
