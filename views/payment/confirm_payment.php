<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">💳 Payment Page</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Total Amount: RM <?= number_format($order['total'], 2) ?></h3>
            <div class="text-end">
                <div style="font-size: 1rem; color: #666;">Your Balance:</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #28a745;">
                    RM <?= number_format($user_balance, 2) ?>
                </div>
            </div>
        </div>

        <?php if (!$payment_success): ?>
            <form method="POST" action="<?= BASE_URL ?>/routes/payment.php">
                <div class="mb-3">
                    <label for="payment_type" class="form-label">Payment Method</label>
                    <select class="form-select" id="
                    payment_type" name="payment_type" required>
                        <option value="" disabled selected>Select payment method</option>
                        <option value="TNG eWallet">TNG eWallet</option>
                        <option value="GrabPay">GrabPay</option>
                        <option value="Online Banking">Online Banking</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Payment successful!
            </div>
        <?php endif; ?>
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
            
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($payment_success): ?>
    <script>
        var modal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
        modal.show();
    </script>
<?php endif; ?>


<?php
require_once __DIR__ . '/../../includes/footer.php';
?>