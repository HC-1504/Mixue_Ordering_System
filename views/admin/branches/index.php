<?php require_once __DIR__ . '/../_header.php'; ?>

<h1>Branch Management</h1>
<?= $feedback; // Display feedback messages from controller ?>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-<?= $edit_branch ? 'edit' : 'plus'; ?>"></i>
        <?= $edit_branch ? 'Edit Branch' : 'Add New Branch'; ?>
    </div>
    <div class="card-body">
        <form action="branches.php" method="POST">
            <input type="hidden" name="id" value="<?= $edit_branch->id ?? ''; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Branch Name</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($edit_branch->name ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($edit_branch->phone ?? ''); ?>">
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" name="address" id="address" rows="3" required><?= htmlspecialchars($edit_branch->address ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $edit_branch ? 'Update Branch' : 'Add Branch'; ?>
            </button>
            <?php if ($edit_branch): ?>
                <a href="branches.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-store"></i> Existing Branches</div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Name</th><th>Address</th><th>Phone</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($branches as $branch): ?>
                <tr>
                    <td><?= htmlspecialchars($branch['name']); ?></td>
                    <td><?= nl2br(htmlspecialchars($branch['address'])); ?></td>
                    <td><?= htmlspecialchars($branch['phone']); ?></td>
                    <td>
                        <a href="branches.php?action=edit&id=<?= $branch['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                        <a href="branches.php?action=delete&id=<?= $branch['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>



<?php require_once __DIR__ . '/../_footer.php'; ?>