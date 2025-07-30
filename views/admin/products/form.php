<?php require_once __DIR__ . '/../_header.php'; ?>

<?php
$is_edit = $product !== null;
$action_url = $is_edit ? "products.php?action=update&id={$product->id}" : "products.php?action=store";
?>

<h1><?= $is_edit ? 'Edit Product' : 'Add New Product' ?></h1>

<form action="<?= $action_url ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product->name ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= (($product->category_id ?? '') == $cat->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product->description ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (RM)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product->price ?? '') ?>" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="is_available" name="is_available" <?= ($product->is_available ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_available">Available</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Product Image</div>
                <div class="card-body text-center">
                    <img src="uploads/<?= htmlspecialchars($product->image ?? 'default.jpg'); ?>" id="image-preview" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="document.getElementById('image-preview').src = window.URL.createObjectURL(this.files[0])">
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $is_edit ? 'Update Product' : 'Create Product' ?></button>
        <a href="products.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/../_footer.php'; ?>