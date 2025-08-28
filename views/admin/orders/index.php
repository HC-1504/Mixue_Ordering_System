<?php require_once __DIR__ . '/../_header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-shopping-cart"></i> Order Management</h4>
                    <span class="badge bg-light text-dark">Total Orders: <?= count($orders) ?></span>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Status Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="btn-group" role="group" aria-label="Status filter">
                                <button type="button" class="btn btn-outline-primary active" onclick="filterOrders('all')">All</button>
                                <button type="button" class="btn btn-outline-warning" onclick="filterOrders('Pending')">Pending</button>
                                <button type="button" class="btn btn-outline-info" onclick="filterOrders('Preparing')">Preparing</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="filterOrders('Out for Delivery')">Out for Delivery</button>
                                <button type="button" class="btn btn-outline-success" onclick="filterOrders('Completed')">Completed</button>
                                <button type="button" class="btn btn-outline-danger" onclick="filterOrders('Cancelled')">Cancelled</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="ordersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Branch</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr data-status="<?= htmlspecialchars($order->status) ?>">
                                        <td><strong>#<?= htmlspecialchars($order->id) ?></strong></td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($order->customer_name ?? 'Guest') ?></strong>
                                                <?php if ($order->customer_email): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($order->customer_email) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($order->phone) ?></td>
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
                                        <td><?= htmlspecialchars($order->branch_name ?? 'N/A') ?></td>
                                        <td><strong>RM <?= number_format($order->total, 2) ?></strong></td>
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
                                            <span class="badge bg-<?= $color ?>">
                                                <?= htmlspecialchars($order->status) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y H:i', strtotime($order->created_at)) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="orders.php?action=view&id=<?= $order->id ?>"
                                                    class="btn btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (!in_array($order->status, ['Completed', 'Cancelled'])): ?>
                                                    <button type="button" class="btn btn-outline-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#statusModal"
                                                        data-order-id="<?= $order->id ?>"
                                                        data-current-status="<?= htmlspecialchars($order->status) ?>"
                                                        title="Update Status">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders found</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="orders.php?action=update_status" method="POST">
                <div class="modal-body">
                    <p>Update status for Order #<strong id="modal-order-id"></strong></p>

                    <div class="mb-3">
                        <label for="new_status" class="form-label">New Status</label>
                        <select class="form-select" name="new_status" id="new_status" required>
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Preparing">Preparing</option>
                            <option value="Out for Delivery">Out for Delivery</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="alert alert-warning" id="refund-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Notice:</strong> Changing order status to "Cancelled" will automatically refund the user.
                    </div>

                    <input type="hidden" name="order_id" id="modal-order-id-input">
                    <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusModal = document.getElementById('statusModal');
        const statusSelect = document.getElementById('new_status');
        const refundWarning = document.getElementById('refund-warning');
        
        if (statusModal) {
            statusModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');
                const currentStatus = button.getAttribute('data-current-status');

                // Update modal content
                document.getElementById('modal-order-id').textContent = orderId;
                document.getElementById('modal-order-id-input').value = orderId;

                // Set current status as selected
                statusSelect.value = currentStatus;
                
                // Hide warning initially
                refundWarning.style.display = 'none';
            });
        }
        
        // Show/hide refund warning based on status selection
        if (statusSelect && refundWarning) {
            statusSelect.addEventListener('change', function() {
                if (this.value === 'Cancelled') {
                    refundWarning.style.display = 'block';
                } else {
                    refundWarning.style.display = 'none';
                }
            });
        }
    });

    function filterOrders(status) {
        const table = document.getElementById('ordersTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        // Update active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        for (let row of rows) {
            if (status === 'all' || row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
</script>

<?php require_once __DIR__ . '/../_footer.php'; ?>