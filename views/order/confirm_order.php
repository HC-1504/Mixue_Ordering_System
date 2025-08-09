<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">🧾 Confirm Your Order</h1>

    <form method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>

        <?php if ($type === 'delivery'): ?>
            <div class="mb-3">
                <label for="address" class="form-label">Delivery Address</label>
                <textarea name="address" id="address" class="form-control" required></textarea>
            </div>
        <?php elseif ($type === 'pickup'): ?>
            <div class="mb-3">
                <label for="branch_id" class="form-label">Pickup Branch</label>
                <select name="branch_id" id="branch_id" class="form-select" required>
                    <option value="">Select a branch</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= htmlspecialchars($branch['id']) ?>"><?= htmlspecialchars($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
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

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>