<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">Order Details</h1>
    <ul class="list-group">
        <li class="list-group-item"><strong>Date:</strong> <?= htmlspecialchars($order['created_at'] ?? '-') ?></li>
        <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($order['phone'] ?? '-') ?></li>
        <li class="list-group-item"><strong>Type:</strong> <?= htmlspecialchars($order['type'] ?? '-') ?></li>
        <?php if ($order['type'] === 'pickup'): ?>
            <li class="list-group-item"><strong>Pickup Location:</strong> <?= htmlspecialchars($order['branch_name'] ?? '-') ?></li>
            <li class="list-group-item list-group-item-info"><strong>Pickup Code:</strong> #<?= htmlspecialchars($order['pickup_sequence'] ?? '-') ?></li>
        <?php else: ?>
            <li class="list-group-item"><strong>Delivery Address:</strong> <?= htmlspecialchars($order['address'] ?? '-') ?></li>
            <li class="list-group-item"><strong>Delivery Fee:</strong> RM <?= number_format($order['delivery_fee'] ?? 0, 2) ?></li>
        <?php endif; ?>
        <li class="list-group-item"><strong>Total (Included Delivery Fee if any):</strong> RM <?= number_format($order['total'] ?? 0, 2) ?></li>
        <li class="list-group-item"><strong>Status:</strong> <?= htmlspecialchars($order['status'] ?? '-') ?></li>
    </ul>

    <?php if (!empty($details)): ?>
        <h3 class="mt-4">Products</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Temperature</th>
                    <th>Sugar</th>
                    <th>Unit Price (RM)</th>
                    <th>Subtotal (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $item): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($item['product_name']) ?><br>
                            <img src="<?= BASE_URL ?>/admin/uploads/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-fluid" style="max-width: 100px;">
                        </td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['temperature']) ?></td>
                        <td><?= htmlspecialchars($item['sugar']) ?></td>
                        <td><?= number_format($item['unit_price'], 2) ?></td>
                        <td><?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Subtotal (Products Only):</th>
                    <th>RM <?= number_format($subtotal, 2) ?></th>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p class="mt-4">No products found for this order.</p>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="<?= BASE_URL ?>/profile.php" class="btn btn-secondary">← Back to Order History</a>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>