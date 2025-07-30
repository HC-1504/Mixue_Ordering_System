<?php require_once __DIR__ . '/../_header.php'; ?>

<h1>Category Management</h1>
<?= $feedback; // Display feedback messages from controller ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-<?= $edit_category ? 'edit' : 'plus'; ?>"></i>
                <?= $edit_category ? 'Edit Category' : 'Add New Category'; ?>
            </div>
            <div class="card-body">
                <form action="categories.php" method="POST">
                    <input type="hidden" name="id" value="<?= $edit_category->id ?? ''; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($edit_category->name ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $edit_category ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($edit_category): ?>
                    <a href="categories.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-list"></i> Existing Categories</div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category->id; ?></td>
                            <td><?= htmlspecialchars($category->name); ?></td>
                            <td>
                                <a href="categories.php?action=edit&id=<?= $category->id; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                <a href="categories.php?action=delete&id=<?= $category->id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../_footer.php'; ?>