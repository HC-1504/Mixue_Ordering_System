<?php require_once '_header.php'; ?>

<h1 class="mb-4">Admin Dashboard</h1>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-header"><i class="fas fa-box"></i> Total Products</div>
            <div class="card-body">
                <h5 class="card-title"><?= $data['product_count']; ?></h5>
                <a href="products.php" class="btn btn-light">Manage Products</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-header"><i class="fas fa-tags"></i> Total Categories</div>
            <div class="card-body">
                <h5 class="card-title"><?= $data['category_count']; ?></h5>
                <a href="categories.php" class="btn btn-light">Manage Categories</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header"><i class="fas fa-store"></i> Total Branches</div>
            <div class="card-body">
                <h5 class="card-title"><?= $data['branch_count']; ?></h5>
                <a href="branches.php" class="btn btn-light">Manage Branches</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header"><i class="fas fa-shopping-cart"></i> Total Orders</div>
            <div class="card-body">
                <h5 class="card-title"><?= $data['order_count']; ?></h5>
                <a href="orders.php" class="btn btn-light">View Orders</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mt-4">
            <div class="card-header"><i class="fas fa-chart-bar"></i> Quick Actions</div>
            <div class="card-body">
                <a href="products.php?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
                <a href="categories.php" class="btn btn-secondary"><i class="fas fa-plus"></i> Add New Category</a>
                <a href="branches.php" class="btn btn-dark"><i class="fas fa-plus"></i> Add New Branch</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mt-4">
            <div class="card-header"><i class="fas fa-clock"></i> Recent Orders</div>
            <div class="card-body">
                <?php if (!empty($data['recent_orders'])): ?>
                    <?php foreach ($data['recent_orders'] as $order): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>Order #<?= $order->id ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($order->user_name) ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?= $order->status === 'Completed' ? 'success' : ($order->status === 'Pending' ? 'warning' : 'info') ?>">
                                    <?= $order->status ?>
                                </span><br>
                                <small class="text-muted">RM <?= number_format($order->total, 2) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="orders.php" class="btn btn-sm btn-outline-primary">View All Orders</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No recent orders</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>