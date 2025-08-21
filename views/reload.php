<?php
// views/reload.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
    exit;
}
?>

<div class="container" style="max-width: 500px; margin: 2rem auto;">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4"><i class="fas fa-wallet"></i> Reload Money</h3>

            <div class="mb-3 text-center">
                <span style="font-size:1.2rem; color:#888;">Current Balance:</span><br>
                <span style="font-size:2rem; font-weight:700; color:#28a745;">RM <?= number_format($user->balance ?? 0, 2) ?></span>
            </div>

            <!-- Display success or error messages -->
            <?php if (isset($_GET['success_message'])): ?>
                <div class="alert alert-success text-center"><?= htmlspecialchars($_GET['success_message']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error_message'])): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($_GET['error_message']) ?></div>
            <?php endif; ?>

            <!-- Stripe Payment Form -->
            <form id="payment-form">
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount to Reload (RM) - minimum RM 2.00</label>
                    <input type="number" step="0.50" min="2.00" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                </div>

                <!-- A container for the Stripe Payment Element -->
                <div id="payment-element"></div>

                <button type="submit" id="submit" class="btn btn-success w-100 mt-3">
                    <span id="button-text">Reload Now</span>
                    <span id="spinner" style="display: none;">Processing...</span>
                </button>
                <div id="payment-message" class="mt-2 text-center"></div>
            </form>

            <a href="<?= BASE_URL ?>/profile.php" class="btn btn-secondary w-100 mt-2">Back to Profile</a>
        </div>
    </div>
</div>

<!-- Include Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const stripe = Stripe('<?= STRIPE_PUBLISHABLE_KEY ?>');
        let elements;
        let clientSecret;
        let paymentElement; // Declare paymentElement here to make it accessible

        const form = document.getElementById('payment-form');
        const amountInput = document.getElementById('amount');
        const submitButton = document.getElementById('submit');
        const paymentMessage = document.getElementById('payment-message');

        // Function to initialize the payment process
        async function initialize() {
            const amount = amountInput.value;
            if (!amount || amount <= 0) {
                paymentMessage.textContent = 'Please enter a valid amount.';
                return;
            }

            // Disable the form while creating the PaymentIntent
            setLoading(true);

            try {
                const response = await fetch('<?= BASE_URL ?>/routes/create_payment_intent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: amount,
                        purpose: 'reload'
                    })
                });

                const data = await response.json();

                if (data.error) {
                    paymentMessage.textContent = data.error;
                    setLoading(false);
                    return;
                }

                clientSecret = data.clientSecret;

                // If elements and paymentElement already exist, update the existing Payment Element
                if (elements && paymentElement) {
                    paymentElement.update({ clientSecret: clientSecret });
                } else {
                    // Create and mount the Payment Element for the first time
                    elements = stripe.elements({
                        clientSecret
                    });
                    paymentElement = elements.create('payment'); // Assign to the globally accessible variable
                    paymentElement.mount('#payment-element');
                }

                paymentMessage.textContent = ''; // Clear previous messages

            } catch (error) {
                console.error('Error:', error);
                paymentMessage.textContent = 'An unexpected error occurred.';
            } finally {
                setLoading(false);
            }
        }

        // Initialize on amount change (with a debounce to avoid rapid calls)
        let debounceTimer;
        amountInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(initialize, 500);
        });

        // Handle form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            setLoading(true);

            if (!elements) {
                paymentMessage.textContent = 'Please enter an amount first.';
                setLoading(false);
                return;
            }

            const {
                error
            } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: window.location.href.split('?')[0] + '?amount=' + amountInput.value,
                },
            });

            if (error) {
                paymentMessage.textContent = error.message;
            } else {
                paymentMessage.textContent = 'Payment is processing...';
            }

            setLoading(false);
        });

        function setLoading(isLoading) {
            submitButton.disabled = isLoading;
            document.getElementById('spinner').style.display = isLoading ? 'inline' : 'none';
            document.getElementById('button-text').style.display = isLoading ? 'none' : 'inline';
        }
    });
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>