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

<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const stripe = Stripe('<?= STRIPE_PUBLISHABLE_KEY ?>');
        let elements;

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const spinner = submitButton.querySelector('.spinner-border');
        const buttonText = submitButton.querySelector('.button-text');
        const paymentError = document.getElementById('payment-error');

        // Handle the redirect back from Stripe first
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('payment_success') === 'true') {
            // This is a redirect back from a successful payment.
            // Show the success modal and clear the local cart session.
            var modal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
            modal.show();
            
            // Hide the form and error messages to prevent confusion
            form.style.display = 'none';
            paymentError.style.display = 'none';

            // Clear the cart session on the server
            fetch('<?= BASE_URL ?>/api/clear_cart.php');

            return; // Stop further script execution
        }
        
        // If we're not on a redirect, initialize the payment form.
        initialize();
        form.addEventListener('submit', handleSubmit);

        // Fetches a payment intent and captures the client secret
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
            const { clientSecret } = await fetch('<?= BASE_URL ?>/routes/create_payment_intent.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: <?= $order['total'] ?>,
                    purpose: 'order'
                }),
            }).then((res) => res.json());

            elements = stripe.elements({ clientSecret });

            const paymentElementOptions = {
                layout: "tabs"
            };

            const paymentElement = elements.create("payment", paymentElementOptions);
            paymentElement.mount("#payment-element");
        }

        async function handleSubmit(e) {
            e.preventDefault();
            setLoading(true);

            const { error } = await stripe.confirmPayment({
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
