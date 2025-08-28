<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">💳 Secure Payment</h1>

    <div id="payment-error" class="alert alert-danger" style="display: none;"></div>

    <div class="card p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Total Amount: RM <?= number_format($order['total'], 2) ?></h3>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Your Mixue Wallet Balance: RM <?= number_format($user_balance, 2) ?></h3>
        </div>

        <div class="payment-method-selection mb-4">
            <h4>Select Payment Method</h4>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="stripe-payment" value="stripe" checked>
                <label class="form-check-label" for="stripe-payment">
                    Credit/Debit Card or E-Wallets (via Stripe)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="wallet-payment" value="wallet" <?= $user_balance < $order['total'] ? 'disabled' : '' ?>>
                <label class="form-check-label" for="wallet-payment">
                    Mixue Wallet
                    <?php if ($user_balance < $order['total']): ?>
                        <span class="text-danger">- Insufficient balance</span>
                    <?php endif; ?>
                </label>
            </div>
        </div>

        <div id="stripe-payment-container">
            <form id="payment-form">
                <div id="payment-element" class="mb-3">
                    <!-- A Stripe Payment Element will be inserted here. -->
                </div>

                <button id="submit-button" class="btn btn-success w-100">
                    <div class="spinner-border spinner-border-sm" role="status" style="display: none;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="button-text">Pay Now</span>
                </button>
            </form>
        </div>

        <div id="wallet-payment-container" style="display: none;">
            <form id="wallet-form">
                <button id="wallet-submit-button" class="btn btn-primary w-100">Pay with Mixue Wallet</button>
            </form>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/routes/cart.php" class="btn btn-secondary">← Back to Cart</a>
    </div>
</div>

<!-- Payment Success Modal -->
<div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentSuccessModalLabel">Payment Successful</h5>
            </div>
            <div class="modal-body text-center">
                <p>Thank you! Your payment was successful.</p>
                <p>You can now view your order history.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="<?= BASE_URL ?>/profile.php" class="btn btn-primary">View Order History</a>
            </div>
        </div>
    </div>
</div>

<!-- Payment Failure Modal -->
<div class="modal fade" id="paymentFailureModal" tabindex="-1" aria-labelledby="paymentFailureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentFailureModalLabel">Payment Failed</h5>
            </div>
            <div class="modal-body text-center">
                <p id="failure-message"></p>
                <p>Please ensure you have sufficient balance or try again.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="<?= BASE_URL ?>/profile.php" class="btn btn-primary">Go to Profile (Reload)</a>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        // Payment method selection
        const stripePaymentRadio = document.getElementById('stripe-payment');
        const walletPaymentRadio = document.getElementById('wallet-payment');
        const stripePaymentContainer = document.getElementById('stripe-payment-container');
        const walletPaymentContainer = document.getElementById('wallet-payment-container');

        stripePaymentRadio.addEventListener('change', () => {
            if (stripePaymentRadio.checked) {
                stripePaymentContainer.style.display = 'block';
                walletPaymentContainer.style.display = 'none';
            }
        });

        walletPaymentRadio.addEventListener('change', () => {
            if (walletPaymentRadio.checked) {
                stripePaymentContainer.style.display = 'none';
                walletPaymentContainer.style.display = 'block';
            }
        });

        const stripe = Stripe('<?= STRIPE_PUBLISHABLE_KEY ?>');
        let elements;

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const spinner = submitButton.querySelector('.spinner-border');
        const buttonText = submitButton.querySelector('.button-text');
        const paymentError = document.getElementById('payment-error');

        // Handle the redirect back from Stripe first
        const urlParams = new URLSearchParams(window.location.search);
        const paymentIntentId = urlParams.get('payment_intent'); // Get payment_intent ID from URL

        if (urlParams.get('payment_success') === 'true' && paymentIntentId) {
            // Hide the form and error messages to prevent confusion
            form.style.display = 'none';
            paymentError.style.display = 'none';

            const successModal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
            const failureModal = new bootstrap.Modal(document.getElementById('paymentFailureModal'));
            const failureMessageElement = document.getElementById('failure-message');

            // Finalize payment on the server
            try {
                const response = await fetch('<?= BASE_URL ?>/routes/finalize_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        paymentIntentId: paymentIntentId
                    })
                });
                const result = await response.json();

                if (result.success) {
                    console.log('Payment finalized successfully on server.');
                    successModal.show(); // Show success modal
                    // Clear the cart session on the server after successful finalization
                    fetch('<?= BASE_URL ?>/api/clear_cart.php');
                } else {
                    console.error('Server-side finalization failed:', result.error);
                    failureMessageElement.textContent = result.error; // Set specific error message
                    failureModal.show(); // Show failure modal
                }
            } catch (error) {
                console.error('Error during server-side finalization:', error);
                failureMessageElement.textContent = 'An unexpected error occurred during payment finalization.';
                failureModal.show(); // Show failure modal
            }

            return; // Stop further script execution
        }

        // If we're not on a redirect, initialize the payment form.
        initialize();
        form.addEventListener('submit', handleSubmit);

        const walletForm = document.getElementById('wallet-form');
        walletForm.addEventListener('submit', handleWalletSubmit);

        async function handleWalletSubmit(e) {
            e.preventDefault();
            setLoading(true);

            const successModal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
            const failureModal = new bootstrap.Modal(document.getElementById('paymentFailureModal'));
            const failureMessageElement = document.getElementById('failure-message');

            try {
                const response = await fetch('<?= BASE_URL ?>/routes/payment.php?method=wallet', {
                    method: 'POST',
                });
                const result = await response.json();

                if (result.success) {
                    successModal.show();
                    fetch('<?= BASE_URL ?>/api/clear_cart.php');
                } else {
                    failureMessageElement.textContent = result.error;
                    failureModal.show();
                }
            } catch (error) {
                failureMessageElement.textContent = 'An unexpected error occurred.';
                failureModal.show();
            }

            setLoading(false);
        }
        async function initialize() {
            // Step 1: Create a pending order on your server
            const orderResponse = await fetch('<?= BASE_URL ?>/routes/payment.php', {
                method: 'POST',
            }).then((res) => res.json());

            if (orderResponse.error) {
                showError(orderResponse.error);
                return;
            }

            // Step 2: Create a Payment Intent on your server
            const {
                clientSecret
            } = await fetch('<?= BASE_URL ?>/routes/create_payment_intent.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    amount: <?= $order['total'] ?>,
                    purpose: 'order'
                }),
            }).then((res) => res.json());

            elements = stripe.elements({
                clientSecret
            });

            const paymentElementOptions = {
                layout: "tabs"
            };

            const paymentElement = elements.create("payment", paymentElementOptions);
            paymentElement.mount("#payment-element");
        }

        async function handleSubmit(e) {
            e.preventDefault();
            setLoading(true);

            const {
                error
            } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    // Make sure to change this to your payment completion page
                    return_url: window.location.href.split('?')[0] + "?payment_success=true",
                },
            });

            // This point will only be reached if there is an immediate error when
            // confirming the payment. Otherwise, your customer will be redirected to
            // your `return_url`. For some payment methods like iDEAL, your customer will
            // be redirected to an intermediate site first to authorize the payment, then
            // redirected to the `return_url`.
            if (error.type === "card_error" || error.type === "validation_error") {
                showError(error.message);
            } else {
                showError("An unexpected error occurred.");
            }

            setLoading(false);
        }

        function setLoading(isLoading) {
            submitButton.disabled = isLoading;
            spinner.style.display = isLoading ? 'inline-block' : 'none';
            buttonText.textContent = isLoading ? 'Processing...' : 'Pay Now';
        }

        function showError(message) {
            paymentError.textContent = message;
            paymentError.style.display = 'block';
        }
    });
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>