<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">🛒 Your Cart</h1>

    <?php // If the cart is not empty, display the table of items
    if (!empty($cartItems)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Temperature</th>
                    <th>Sugar</th>
                    <th>Unit Price (RM)</th>
                    <th>Quantity</th>
                    <th>Subtotal (RM)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php // Loop through each item in the cart
                foreach ($cartItems as $key => $item): ?>
                    <?php
                    $product = $item['product'];
                    $subtotal = $item['subtotal'];
                    ?>

                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['temperature']); ?></td>
                        <td><?php echo htmlspecialchars($item['sugar']); ?></td>
                        <td><?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <!-- Edit Button: triggers the modal and passes current item values via data attributes -->
                            <button
                                type="button"
                                class="btn btn-warning btn-sm edit-cart-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#editCartModal"
                                data-index="<?php echo $key; ?>"
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>"
                                data-category="<?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES); ?>"
                                data-price="<?php echo number_format($product['price'], 2); ?>"
                                data-quantity="<?php echo $item['quantity']; ?>"
                                data-temperature="<?php echo htmlspecialchars($item['temperature'], ENT_QUOTES); ?>"
                                data-sugar="<?php echo htmlspecialchars($item['sugar'], ENT_QUOTES); ?>">
                                Edit
                            </button>

                            <!-- Remove Button: triggers confirmation modal -->
                            <button type="button" class="btn btn-danger btn-sm remove-cart-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#removeCartModal"
                                data-index="<?php echo $key; ?>"
                                data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="6" class="text-end">Total</th>
                    <th>RM <?php echo number_format($total, 2); ?></th>
                </tr>
            </tbody>
        </table>

        <div class="text-end">
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeAllModal">
                Remove All Items
            </button>
        </div>

        <div class="text-center">
            <!-- Button to proceed to checkout -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutTypeModal">
                Proceed to Checkout
            </button>
        </div>
    <?php else: ?>
        <!-- Message if the cart is empty -->
        <p class="text-center">Your cart is empty.</p>
    <?php endif; ?>

    <div class="text-center mt-3">
        <!-- Button to go back to the menu -->
        <a href="<?= BASE_URL ?>/routes/menu.php" class="btn btn-secondary">← Back to Menu</a>
    </div>
</div>

<!-- Edit Cart Item Modal -->
<div class="modal fade" id="editCartModal" tabindex="-1" aria-labelledby="editCartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= BASE_URL ?>/routes/cart.php">
                <input type="hidden" name="action" value="update">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCartModalLabel">Edit Cart Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden fields for item index and product ID -->
                    <input type="hidden" name="index" id="edit-modal-index">
                    <input type="hidden" name="id" id="edit-modal-product-id">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="edit-modal-product-name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (RM)</label>
                        <input type="text" class="form-control" id="edit-modal-product-price" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" id="edit-modal-product-qty" value="1" min="1" required>
                    </div>
                    <!-- Hot/Cold Option -->
                    <div class="mb-3 temperature-field">
                        <label class="form-label">Temperature</label>
                        <select class="form-select" name="temperature" id="edit-modal-temperature" required>
                            <option value="Hot">Hot</option>
                            <option value="Cold">Cold</option>
                        </select>
                    </div>
                    <!-- Sugar Level Option -->
                    <div class="mb-3">
                        <label class="form-label">Sugar Level</label>
                        <select class="form-select" name="sugar" id="edit-modal-sugar" required>
                            <option value="100%">100%</option>
                            <option value="50%">50%</option>
                            <option value="0%">0%</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove Cart Item Confirmation Modal -->
<div class="modal fade" id="removeCartModal" tabindex="-1" aria-labelledby="removeCartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= BASE_URL ?>/routes/cart.php">
                <input type="hidden" name="action" value="remove">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeCartModalLabel">Remove Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="index" id="remove-modal-index">
                    <p>Are you sure you want to remove <strong id="remove-modal-product-name"></strong> from your cart?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove All Items Confirmation Modal -->
<div class="modal fade" id="removeAllModal" tabindex="-1" aria-labelledby="removeAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= BASE_URL ?>/routes/cart.php">
                <input type="hidden" name="action" value="remove_all">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeAllModalLabel">Remove All Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong>all items</strong> from your cart?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove All</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Checkout Type Modal -->
<div class="modal fade" id="checkoutTypeModal" tabindex="-1" aria-labelledby="checkoutTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutTypeModalLabel">Choose Order Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>How would you like to receive your order?</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= BASE_URL ?>/routes/order.php?action=confirm&type=delivery" class="btn btn-outline-primary">Delivery</a>
                    <a href="<?= BASE_URL ?>/routes/order.php?action=confirm&type=pickup" class="btn btn-outline-success">Pickup</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle modal population -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // When an Edit button is clicked, fill the modal with the item's current info
    document.querySelectorAll('.edit-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('edit-modal-index').value = this.getAttribute('data-index');
            document.getElementById('edit-modal-product-id').value = this.getAttribute('data-id');
            document.getElementById('edit-modal-product-name').value = this.getAttribute('data-name');
            document.getElementById('edit-modal-product-price').value = this.getAttribute('data-price');
            document.getElementById('edit-modal-product-qty').value = this.getAttribute('data-quantity');
            document.getElementById('edit-modal-temperature').value = this.getAttribute('data-temperature');
            document.getElementById('edit-modal-sugar').value = this.getAttribute('data-sugar');

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
        });
    });

    // When a Remove button is clicked, fill the confirmation modal with the item's info
    document.querySelectorAll('.remove-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('remove-modal-index').value = this.getAttribute('data-index');
            document.getElementById('remove-modal-product-name').textContent = this.getAttribute('data-name');
        });
    });
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>