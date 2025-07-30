<?php require_once __DIR__ . '/../_header.php'; ?>

<h1>Product Management</h1>
<p><a href="products.php?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a></p>

<?php if (Session::get('feedback')): ?>
    <div class="alert alert-info"><?php echo Session::get('feedback'); Session::unset('feedback'); ?></div>
<?php endif; ?>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-search"></i> Search & Filter Products
    </div>
    <div class="card-body">
        <form method="GET" action="products.php" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                       placeholder="Search by name or description...">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->id ?>" 
                                <?= (isset($_GET['category']) && $_GET['category'] == $category->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>Available</option>
                    <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>Unavailable</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
        <?php if (!empty($_GET['search']) || !empty($_GET['category']) || (isset($_GET['status']) && $_GET['status'] !== '')): ?>
            <div class="mt-3">
                <a href="products.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        All Products 
        <?php if (!empty($_GET['search']) || !empty($_GET['category']) || (isset($_GET['status']) && $_GET['status'] !== '')): ?>
            <span class="badge bg-info"><?= count($products) ?> result(s)</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price (RM)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><img src="uploads/<?= htmlspecialchars($product->image); ?>" alt="<?= htmlspecialchars($product->name); ?>" width="50" height="50" style="object-fit: cover;" class="rounded"></td>
                        <td><?= htmlspecialchars($product->name); ?></td>
                        <td><?= htmlspecialchars($product->category_name); ?></td>
                        <td><?= number_format($product->price, 2); ?></td>
                        <td>
                            <?php if ($product->is_available): ?>
                                <span class="badge bg-success">Available</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Unavailable</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="products.php?action=edit&id=<?= $product->id; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <a href="products.php?action=delete&id=<?= $product->id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../_footer.php'; ?>