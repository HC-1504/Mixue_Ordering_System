<?php require_once __DIR__ . '/../_header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="orders.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-receipt"></i> Order #<?= htmlspecialchars($order->id) ?> Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Information -->
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td>#<?= htmlspecialchars($order->id) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'Pending' => 'warning',
                                            'Preparing' => 'info',
                                            'Out for Delivery' => 'secondary',
                                            'Completed' => 'success',
                                            'Cancelled' => 'danger'
                                        ];
                                        $color = $statusColors[$order->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?> fs-6">
                                            <?= htmlspecialchars($order->status) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        <?php
                                                                                 $typeIcons = [
                                             'delivery' => 'fas fa-truck',
                                             'pickup' => 'fas fa-hand-holding'
                                         ];
                                        $icon = $typeIcons[strtolower($order->type)] ?? 'fas fa-question';
                                        ?>
                                        <i class="<?= $icon ?>"></i> <?= htmlspecialchars($order->type) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Branch:</strong></td>
                                    <td><?= htmlspecialchars($order->branch_name ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><h5 class="text-success">RM <?= number_format($order->total, 2) ?></h5></td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td><?= date('F j, Y H:i:s', strtotime($order->created_at)) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?= htmlspecialchars($order->customer_name ?? 'Guest Customer') ?></td>
                                </tr>
                                <?php if ($order->customer_email): ?>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?= htmlspecialchars($order->customer_email) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?= htmlspecialchars($order->phone) ?></td>
                                </tr>
                                <?php if ($order->address): ?>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?= htmlspecialchars($order->address) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Order Items -->
                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Temperature</th>
                                    <th>Sugar Level</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $grandTotal = 0; ?>
                                <?php foreach ($orderDetails as $item): ?>
                                    <?php $subtotal = $item->price * $item->quantity; ?>
                                    <?php $grandTotal += $subtotal; ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($item->product_name) ?></strong></td>
                                        <td><?= htmlspecialchars($item->quantity) ?></td>
                                        <td>RM <?= number_format($item->price, 2) ?></td>
                                        <td>
                                            <?php if ($item->temperature): ?>
                                                <span class="badge bg-<?= $item->temperature === 'Hot' ? 'danger' : 'primary' ?>">
                                                    <?= htmlspecialchars($item->temperature) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item->sugar): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <?= htmlspecialchars($item->sugar) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong>RM <?= number_format($subtotal, 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th>RM <?= number_format($order->total, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Action Buttons -->
                    <?php if (!in_array($order->status, ['Completed', 'Cancelled'])): ?>
                        <div class="mt-4">
                            <h5>Quick Actions</h5>
                            <div class="btn-group" role="group">
                                <?php if ($order->status === 'Pending'): ?>
                                    <button type="button" class="btn btn-info" 
                                            onclick="updateOrderStatus(<?= $order->id ?>, 'Preparing')">
                                        <i class="fas fa-clock"></i> Mark as Preparing
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($order->status === 'Preparing' && strtolower($order->type) === 'delivery'): ?>
                                    <button type="button" class="btn btn-secondary" 
                                            onclick="updateOrderStatus(<?= $order->id ?>, 'Out for Delivery')">
                                        <i class="fas fa-truck"></i> Mark as Out for Delivery
                                    </button>
                                <?php endif; ?>
                                
                                <?php if (in_array($order->status, ['Preparing', 'Out for Delivery']) || ($order->status === 'Preparing' && strtolower($order->type) !== 'delivery')): ?>
                                    <button type="button" class="btn btn-success" 
                                            onclick="updateOrderStatus(<?= $order->id ?>, 'Completed')">
                                        <i class="fas fa-check"></i> Mark as Completed
                                    </button>
                                <?php endif; ?>
                                
                                <button type="button" class="btn btn-danger" 
                                        onclick="updateOrderStatus(<?= $order->id ?>, 'Cancelled')">
                                    <i class="fas fa-times"></i> Cancel Order
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateOrderStatus(orderId, newStatus) {
    if (confirm(`Are you sure you want to change the order status to "${newStatus}"?`)) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'orders.php?action=update_status';
        
        const orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'order_id';
        orderIdInput.value = orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'new_status';
        statusInput.value = newStatus;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_csrf';
        csrfInput.value = '<?= Session::generateCsrfToken() ?>';
        
        form.appendChild(orderIdInput);
        form.appendChild(statusInput);
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../_footer.php'; ?> 