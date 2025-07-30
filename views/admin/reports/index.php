<?php require_once __DIR__ . '/../_header.php'; ?>

<div class="container-fluid mt-4">
    <!-- Filter Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Sales Reports</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="reports.php" class="row g-3">
                        <div class="col-md-4">
                            <label for="range" class="form-label">Date Range</label>
                            <select class="form-select" name="range" id="range">
                                <option value="today" <?= ($_GET['range'] ?? 'today') === 'today' ? 'selected' : '' ?>>Today</option>
                                <option value="yesterday" <?= ($_GET['range'] ?? '') === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                                <option value="week" <?= ($_GET['range'] ?? '') === 'week' ? 'selected' : '' ?>>Last 7 Days</option>
                                <option value="month" <?= ($_GET['range'] ?? '') === 'month' ? 'selected' : '' ?>>Last 30 Days</option>
                                <option value="year" <?= ($_GET['range'] ?? '') === 'year' ? 'selected' : '' ?>>Last Year</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="branch" class="form-label">Branch</label>
                            <select class="form-select" name="branch" id="branch">
                                <option value="all" <?= ($_GET['branch'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Branches</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch->id ?>" <?= ($_GET['branch'] ?? '') == $branch->id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($branch->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= number_format($salesData->total_orders ?? 0) ?></h4>
                            <p class="card-text">Total Orders</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">RM <?= number_format($salesData->total_revenue ?? 0, 2) ?></h4>
                            <p class="card-text">Total Revenue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= number_format($salesData->completed_orders ?? 0) ?></h4>
                            <p class="card-text">Completed Orders</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">RM <?= number_format($salesData->avg_order_value ?? 0, 2) ?></h4>
                            <p class="card-text">Avg Order Value</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Products -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Top Products</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($topProducts)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProducts as $index => $product): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary me-1">#<?= $index + 1 ?></span>
                                                <?= htmlspecialchars($product->product_name) ?>
                                            </td>
                                            <td><?= number_format($product->total_quantity) ?></td>
                                            <td>RM <?= number_format($product->total_revenue, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No sales data available for the selected period.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sales by Status -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Orders by Status</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($salesByStatus)): ?>
                        <?php foreach ($salesByStatus as $status): ?>
                            <?php
                            $statusColors = [
                                'Pending' => 'warning',
                                'Preparing' => 'info',
                                'Out for Delivery' => 'secondary',
                                'Completed' => 'success',
                                'Cancelled' => 'danger'
                            ];
                            $color = $statusColors[$status->status] ?? 'secondary';
                            $percentage = $salesData->total_orders > 0 ? ($status->count / $salesData->total_orders) * 100 : 0;
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($status->status) ?></span>
                                    <span><?= number_format($status->count) ?> orders (<?= number_format($percentage, 1) ?>%)</span>
                                </div>
                                <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No status data available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sales by Branch -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-store"></i> Sales by Branch</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($salesByBranch)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($salesByBranch as $branch): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($branch->branch_name) ?></td>
                                            <td><?= number_format($branch->completed_orders) ?></td>
                                            <td>RM <?= number_format($branch->revenue, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No branch data available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sales by Type -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Sales by Order Type</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($salesByType)): ?>
                        <?php
                        $totalTypeRevenue = array_sum(array_column($salesByType, 'revenue'));
                        $typeIcons = [
                            'delivery' => 'fas fa-truck',
                            'pickup' => 'fas fa-hand-holding'
                        ];
                        ?>
                        <?php foreach ($salesByType as $type): ?>
                            <?php
                            $icon = $typeIcons[strtolower($type->type)] ?? 'fas fa-question';
                            $percentage = $totalTypeRevenue > 0 ? ($type->revenue / $totalTypeRevenue) * 100 : 0;
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="<?= $icon ?> me-2"></i>
                                        <?= htmlspecialchars($type->type) ?>
                                    </div>
                                    <div class="text-end">
                                        <div><?= number_format($type->count) ?> orders</div>
                                        <small class="text-muted">RM <?= number_format($type->revenue, 2) ?></small>
                                    </div>
                                </div>
                                <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No order type data available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-submit form when filters change
document.getElementById('range').addEventListener('change', function() {
    this.form.submit();
});

document.getElementById('branch').addEventListener('change', function() {
    this.form.submit();
});
</script>

<?php require_once __DIR__ . '/../_footer.php'; ?> 