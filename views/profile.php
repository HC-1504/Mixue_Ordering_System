<?php
// views/profile.php - THE NEW, CLEAN "VIEW" VERSION

// The header is included by the main entry point or layout file,
// but we'll assume it's needed here if you access this file directly in some cases.
require_once __DIR__ . '/../includes/header.php';

?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <!-- Display the first letter of the user's name as an avatar -->
            <?= htmlspecialchars(strtoupper(substr($user->name ?? 'U', 0, 1))) ?>
        </div>
        <div class="profile-info">
            <h2><?= htmlspecialchars($user->name ?? 'Guest') ?></h2>
            <p><?= htmlspecialchars($user->email ?? 'no-email@example.com') ?></p>
        </div>
    </div>

    <!-- Display user balance and reload/change password buttons -->
    <div class="card mb-4" style="max-width: 500px; margin: 2rem auto 1rem auto; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <div class="card-body text-center">
            <!-- Display success or error messages for password change -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <?= htmlspecialchars($error) ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h5 class="card-title mb-3">Account Balance</h5>
            <div style="font-size:2.2rem; font-weight:700; color:#28a745; margin-bottom:1rem;">
                RM <?= number_format($user->balance ?? 0, 2) ?>
            </div>
            <div class="d-flex justify-content-center gap-2 mb-2">
                <a href="<?= BASE_URL ?>/reload.php" class="btn btn-success btn-lg"><i class="fas fa-wallet"></i> Reload Money</a>
                <a href="<?= BASE_URL ?>/views/change_password.php" class="btn btn-primary btn-lg"><i class="fas fa-key"></i> Change Password</a>
            </div>
        </div>
    </div>
    <hr style="margin: 2rem 0;">

    <!-- Reload Transaction History Table -->
    <div class="card mb-4" style="max-width: 700px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-history me-2"></i>Reload Transaction History</h5>
            <?php if (!empty($reloads)): ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount (RM)</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reloads as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r->created_at) ?></td>
                                <td style="color:#28a745; font-weight:600;">+<?= number_format($r->amount, 2) ?></td>
                                <td><?= htmlspecialchars($r->payment_type ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center text-muted">No reload transactions yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order History Table -->
    <div class="card mb-4" style="max-width: 900px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-receipt me-2"></i>Order History</h5>
            <!-- Note: The PHP logic to fetch order_history has been removed. -->
            <!-- We now assume the controller has provided the $order_history variable. -->
            <?php if (!empty($order_history)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Order Code</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_history as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['created_at'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($order['type'] ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($order['daily_sequence'])): ?>
                                        <strong>#<?= htmlspecialchars($order['daily_sequence']) ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($order['branch_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($order['status'] ?? '-') ?></td>
                                <td>
                                    <?php if (($order['status'] ?? '-') !== 'Cancelled'): ?>
                                        <a href="<?= BASE_URL ?>/routes/order_details.php?id=<?= urlencode($order['id']) ?>" class="btn btn-warning btn-sm">View Details</a>
                                        <button class="btn btn-info btn-sm reorder-btn" data-order-id="<?= $order['id'] ?>">Re-order</button>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-muted">You have no previous orders.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hidden form for re-ordering -->
    <form id="reorder-form" action="<?= BASE_URL ?>/routes/cart.php" method="POST" style="display: none;">
        <input type="hidden" name="action" value="add_batch">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
        <div id="reorder-items-container"></div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reorderButtons = document.querySelectorAll('.reorder-btn');
        const reorderForm = document.getElementById('reorder-form');
        const reorderItemsContainer = document.getElementById('reorder-items-container');

        reorderButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');

                // Fetch the details for the selected order
                fetch(`<?= BASE_URL ?>/api/order_details.php?id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error || !data.details) {
                            alert('Could not retrieve order details.');
                            return;
                        }

                        // Clear any previous items from the form
                        reorderItemsContainer.innerHTML = '';

                        // Dynamically create hidden inputs for each item
                        data.details.forEach((item, index) => {
                            reorderItemsContainer.innerHTML += `
                            <input type="hidden" name="items[${index}][id]" value="${item.product_id}">
                            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                            <input type="hidden" name="items[${index}][temperature]" value="${item.temperature}">
                            <input type="hidden" name="items[${index}][sugar]" value="${item.sugar}">
                        `;
                        });

                        // Submit the form to add items to the cart
                        reorderForm.submit();
                    })
                    .catch(error => {
                        console.error('Error fetching order details:', error);
                        alert('An error occurred while trying to re-order.');
                    });
            });
        });
    });
</script>

<?php
// Include the main website footer
require_once __DIR__ . '/../includes/footer.php';
?>