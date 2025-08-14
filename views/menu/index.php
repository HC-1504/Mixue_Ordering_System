<!-- http://localhost/Assignment/views/menu.php -->

<?php
require_once '../includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);
?>

<?php // Show a success message if an item was added to the cart
if (isset($_GET['added'])): ?>
    <div id="added-alert" class="alert alert-success text-center">
        <?php echo htmlspecialchars($_GET['added']); ?> added to cart!
    </div>
<?php endif; ?>

<div class="container mt-5">
    <!-- Banner 1 -->
    <div>
        <img src="../assets/images/menu_banner1.jpg" class="img-fluid">
    </div>

    <!-- Banner 2 -->
    <div>
        <img src="../assets/images/menu_banner2.jpg" class="img-fluid">
    </div>
    <!-- Banner 3 -->
    <div style="display: flex; flex-direction: row; gap: 10px;">
        <div>
            <img src="../assets/images/menu_banner3_item1.jpg" class="img-fluid">
        </div>
        <div>
            <img src="../assets/images/menu_banner3_item2.jpg" class="img-fluid">
        </div>
        <div>
            <img src="../assets/images/menu_banner3_item3.png" class="img-fluid">
        </div>
    </div>
    <br><br>
    <!-- Our Products -->
    <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
        <div style="height: 25px;"><img style="height: 25px; object-fit: contain;" src="../assets/images/menu_our_product.png"></div>
        <div style="font-size: 24px; font-weight: bold;">Our Products</div>
    </div>
    <br><br>
    <!-- Menu Tabs -->
    <div>
        <ul class="nav nav-tabs" id="myTab" role="tablist" style="display: flex; flex-direction: row; gap: 10px; align-items: center; justify-content: center;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All Products</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ice-cream-tab" data-bs-toggle="tab" data-bs-target="#ice-cream" type="button" role="tab" aria-controls="ice-cream" aria-selected="false">Fresh Ice Cream and Tea</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="milk-tea-tab" data-bs-toggle="tab" data-bs-target="#milk-tea" type="button" role="tab" aria-controls="milk-tea" aria-selected="false">Milk Tea</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="fresh-tea-tab" data-bs-toggle="tab" data-bs-target="#fresh-tea" type="button" role="tab" aria-controls="fresh-tea" aria-selected="false">Fresh Tea</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="coffee-tab" data-bs-toggle="tab" data-bs-target="#coffee" type="button" role="tab" aria-controls="coffee" aria-selected="false">Coffee</button>
            </li>
        </ul>
    </div>
    <br><br>

    <!-- Tab Content -->
    <div class="tab-content" id="myTabContent">
        <!-- All Products Tab -->
        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
            <div class="row">
                <?php foreach ($products as $row): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="../admin/uploads/<?= htmlspecialchars($row['image'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: contain;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                <p class="card-text">RM <?= number_format($row['price'], 2) ?></p>
                                <small class="text-muted">Category: <?= htmlspecialchars($row['category_name'] ?? 'General') ?></small><br><br>
                                <button
                                    class="btn btn-primary add-to-cart-btn"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                    data-price="<?php echo number_format($row['price'], 2); ?>"
                                    data-category="<?php echo htmlspecialchars($row['category_name'] ?? '', ENT_QUOTES); ?>">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Ice Cream Tab -->
        <div class="tab-pane fade" id="ice-cream" role="tabpanel" aria-labelledby="ice-cream-tab">
            <div class="row">
                <?php foreach ($products as $row): ?>
                    <?php if (stripos($row['category_name'] ?? '', 'ice cream') !== false): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <img src="../admin/uploads/<?= htmlspecialchars($row['image'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: contain;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                    <p class="card-text">RM <?= number_format($row['price'], 2) ?></p>
                                    <small class="text-muted">Category: <?= htmlspecialchars($row['category_name'] ?? 'Ice Cream') ?></small><br><br>
                                    <button
                                        class="btn btn-primary add-to-cart-btn"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                        data-price="<?php echo number_format($row['price'], 2); ?>"
                                        data-category="<?php echo htmlspecialchars($row['category_name'] ?? '', ENT_QUOTES); ?>">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Milk Tea Tab -->
        <div class="tab-pane fade" id="milk-tea" role="tabpanel" aria-labelledby="milk-tea-tab">
            <div class="row">
                <?php foreach ($products as $row): ?>
                    <?php if (stripos($row['category_name'] ?? '', 'milk tea') !== false): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <img src="../admin/uploads/<?= htmlspecialchars($row['image'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: contain;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                    <p class="card-text">RM <?= number_format($row['price'], 2) ?></p>
                                    <small class="text-muted">Category: <?= htmlspecialchars($row['category_name'] ?? 'Milk Tea') ?></small><br><br>
                                    <button
                                        class="btn btn-primary add-to-cart-btn"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                        data-price="<?php echo number_format($row['price'], 2); ?>">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Fresh Tea Tab -->
        <div class="tab-pane fade" id="fresh-tea" role="tabpanel" aria-labelledby="fresh-tea-tab">
            <div class="row">
                <?php foreach ($products as $row): ?>
                    <?php if (stripos($row['category_name'] ?? '', 'fruit drink') !== false): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <img src="../admin/uploads/<?= htmlspecialchars($row['image'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: contain;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                    <p class="card-text">RM <?= number_format($row['price'], 2) ?></p>
                                    <small class="text-muted">Category: <?= htmlspecialchars($row['category_name'] ?? 'Fresh Tea') ?></small><br><br>
                                    <button
                                        class="btn btn-primary add-to-cart-btn"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                        data-price="<?php echo number_format($row['price'], 2); ?>">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Coffee Tab -->
        <div class="tab-pane fade" id="coffee" role="tabpanel" aria-labelledby="coffee-tab">
            <div class="row">
                <?php foreach ($products as $row): ?>
                    <?php if (stripos($row['category_name'] ?? '', 'coffee') !== false): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <img src="../admin/uploads/<?= htmlspecialchars($row['image'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: contain;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                    <p class="card-text">RM <?= number_format($row['price'], 2) ?></p>
                                    <small class="text-muted">Category: <?= htmlspecialchars($row['category_name'] ?? 'Coffee') ?></small><br><br>
                                    <button
                                        class="btn btn-primary add-to-cart-btn"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                        data-price="<?php echo number_format($row['price'], 2); ?>">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        // Pass login status from PHP to JS
        var isLoggedIn = <?= json_encode($is_logged_in) ?>;

        // Hide the alert after 3 seconds
        window.onload = function() {
            var alert = document.getElementById('added-alert');
            if (alert) {
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 3000); // 3000 milliseconds = 3 seconds
            }
        };

        // When an Add to Cart button is clicked, handle login check and modal
        document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
            btn.addEventListener('click', function(event) {
                if (!isLoggedIn) {
                    event.preventDefault();
                    var loginModal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
                    loginModal.show();
                } else {
                    // User is logged in, show the modal as usual
                    document.getElementById('modal-product-id').value = this.getAttribute('data-id');
                    document.getElementById('modal-product-name').value = this.getAttribute('data-name');
                    document.getElementById('modal-product-price').value = this.getAttribute('data-price');
                    document.getElementById('modal-product-qty').value = 1; // Reset quantity

                    // Get category and hide/show temperature field accordingly
                    var category = this.getAttribute('data-category');
                    // Check if product name contains 'ice cream' and make it non-selectable
                    var productName = this.getAttribute('data-name');
                    var temperatureField = document.querySelector('.temperature-field');
                    var temperatureSelect = document.querySelector('select[name="temperature"]');

                    if (category && category.toLowerCase().includes('ice cream') || productName && productName.toLowerCase().includes('ice cream')) {
                        // Lock to 'Cold' for ice cream
                        temperatureSelect.innerHTML = '<option value="Cold" selected>Cold</option>';
                        temperatureSelect.setAttribute('readonly', 'readonly'); // For extra clarity (even though it's not standard on <select>)
                    } else {
                        // Reset to full options for others
                        temperatureSelect.innerHTML = `
                            <option value="Hot">Hot</option>
                            <option value="Cold">Cold</option>
                        `;
                        temperatureSelect.removeAttribute('readonly');
                    }

                    var modal = new bootstrap.Modal(document.getElementById('addToCartModal'));
                    modal.show();
                }
            });
        });
    </script>
    <br><br>
    <!-- Join Us -->
    <div style="display: flex; flex-direction: row; gap: 24px; align-items: center; justify-content: center; padding: 28px 12px; background: #fff7f0; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); margin: 32px 0;">
        <div style="width: 50%;">
            <p style="font-size: 1.7rem; font-weight: bold; color: #e74c3c; margin-bottom: 8px;">Join MIXUE now</p>
            <p style="font-size: 1.05rem; color: #333;">Join MIXUE now for your business opportunity. Scan the QR code to contact us.</p>
        </div>
        <div style="width: 20%; display: flex; justify-content: center;">
            <img style="height: 110px; object-fit: contain; border-radius: 10px; border: 2px solid #e74c3c22; background: #fff;" src="../assets/images/menu_qr.png" class="img-fluid" alt="Join Us QR Code">
        </div>
    </div>
    <br><br>

    <!-- Add to Cart Modal -->
    <div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="<?= BASE_URL ?>/routes/cart.php">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden field for product ID -->
                        <input type="hidden" name="id" id="modal-product-id">
                        <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">

                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" id="modal-product-name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (RM)</label>
                            <input type="text" class="form-control" id="modal-product-price" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" id="modal-product-qty" value="1" min="1" required>
                        </div>
                        <!-- Hot/Cold Option -->
                        <div class="mb-3 temperature-field">
                            <label class="form-label">Temperature</label>
                            <select class="form-select" name="temperature" required>
                                <option value="Hot">Hot</option>
                                <option value="Cold">Cold</option>
                            </select>
                        </div>

                        <!-- Sugar Level Option -->
                        <div class="mb-3">
                            <label class="form-label">Sugar Level</label>
                            <select class="form-select" name="sugar" required>
                                <option value="100%">100%</option>
                                <option value="50%">50%</option>
                                <option value="0%">0%</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Login Required Modal -->
    <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginRequiredModalLabel">Login Required</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You must log in to order. Go to login page?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="<?= BASE_URL ?>/views/login_logout_modules/login.php" class="btn btn-primary">Go to Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to hide the alert after 3 seconds and handle modal population -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    require_once '../includes/footer.php';
    ?>