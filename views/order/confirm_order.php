<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">🧾 Confirm Your Order</h1>

    <form method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" name="phone" id="phone" class="form-control" required maxlength="20" minlength="5">
            <div id="phone-error" class="invalid-feedback">
                Please enter a valid phone number (no letters).
            </div>
        </div>

        <div class="mb-3">
            <label for="branch_id" class="form-label"><?= $type === 'pickup' ? 'Pickup Branch' : 'Delivery Branch' ?></label>
            <select name="branch_id" id="branch_id" class="form-select" required>
                <option value="">Select a branch</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= htmlspecialchars($branch['id']) ?>"><?= htmlspecialchars($branch['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($type === 'delivery'): ?>
            <div class="mb-3">
                <label for="address" class="form-label">Delivery Address</label>
                <textarea name="address" id="address" class="form-control" required></textarea>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-100">Place Order</button>
    </form>

    <!-- 👇 Order summary section -->
    <div class="mt-4 card p-4 bg-light shadow-sm">
        <p>Subtotal: RM <?= number_format($subtotal, 2) ?></p>
        <p>Delivery Fee: RM <?= number_format($deliveryFee, 2) ?></p>
        <p><strong>Total: RM <?= number_format($total, 2) ?></strong></p>
        <p class="text-muted"><?= htmlspecialchars($deliveryNote) ?></p>
    </div>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/routes/cart.php" class="btn btn-secondary">← Back to Cart</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phone-error');

        phoneInput.addEventListener('input', function() {
            // Remove any non-digit characters except for +, -, and space
            phoneInput.value = phoneInput.value.replace(/[^0-9+\-\s]/g, '');

            if (phoneInput.value.length < 5) {
                phoneInput.classList.add('is-invalid');
                phoneError.style.display = 'block';
            } else {
                phoneInput.classList.remove('is-invalid');
                phoneError.style.display = 'none';
            }
        });
    });
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>