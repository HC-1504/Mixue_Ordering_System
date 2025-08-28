<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">🧾 Confirm Your Order</h1>

    <form method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" name="phone" id="phone" class="form-control" required maxlength="20" minlength="5">
            <div id="phone-error" class="invalid-feedback">
                Please enter a valid phone number (no letters).
            </div>
        </div>

        <div class="mb-3">
            <label for="branch_id" class="form-label"><?= $type === 'pickup' ? 'Pickup Branch' : 'Delivery Branch' ?></label>
            <select name="branch_id" id="branch_id" class="form-select" required>
                <option value="">Select a branch</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= htmlspecialchars($branch['id']) ?>"><?= htmlspecialchars($branch['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($type === 'delivery'): ?>
            <div class="mb-3">
                <label for="address" class="form-label">Delivery Address</label>
                <button type="button" id="use-location-btn" class="btn btn-secondary btn-sm mb-2">Use My Current Location</button>
                <textarea name="address" id="address" class="form-control" required></textarea>
                <small id="location-status" class="form-text text-muted"></small>
            </div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-100">Place Order</button>
    </form>

    <!-- 👇 Order summary section -->
    <div class="mt-4 card p-4 bg-light shadow-sm">
        <p>Subtotal: RM <?= number_format($subtotal, 2) ?></p>
        <p>Delivery Fee: RM <?= number_format($deliveryFee, 2) ?></p>
        <p><strong>Total: RM <?= number_format($total, 2) ?></strong></p>
        <p class="text-muted"><?= htmlspecialchars($deliveryNote) ?></p>
    </div>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/routes/cart.php" class="btn btn-secondary">← Back to Cart</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phone-error');

        phoneInput.addEventListener('input', function() {
            // Remove any non-digit characters except for +, -, and space
            phoneInput.value = phoneInput.value.replace(/[^0-9+\-\s]/g, '');

            if (phoneInput.value.length < 5) {
                phoneInput.classList.add('is-invalid');
                phoneError.style.display = 'block';
            } else {
                phoneInput.classList.remove('is-invalid');
                phoneError.style.display = 'none';
            }
        });

        <?php if ($type === 'delivery'): ?>
        const useLocationBtn = document.getElementById('use-location-btn');
        const branchSelect = document.getElementById('branch_id');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const statusText = document.getElementById('location-status');

        useLocationBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                statusText.textContent = 'Geolocation is not supported by your browser.';
                return;
            }

            statusText.textContent = 'Getting your location...';

            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                latInput.value = lat;
                lonInput.value = lon;

                statusText.textContent = `Location found. Fetching details...`;

                const reverseGeocodePromise = fetch(`<?= BASE_URL ?>/api/reverse_geocode.php?latitude=${lat}&longitude=${lon}`).then(res => res.json());
                const findBranchPromise = fetch(`<?= BASE_URL ?>/api/find_nearest_branch.php?latitude=${lat}&longitude=${lon}`).then(res => res.json());

                Promise.all([reverseGeocodePromise, findBranchPromise])
                    .then(([addressData, branchData]) => {
                        let finalStatus = '';

                        // Handle address
                        if (addressData.error) {
                            finalStatus += 'Could not fetch address. ';
                        } else {
                            const addressInput = document.getElementById('address');
                            addressInput.value = addressData.address;
                            finalStatus += 'Address filled. ';
                        }

                        // Handle branch
                        if (branchData.error) {
                            finalStatus += 'Could not find a nearby branch.';
                        } else {
                            branchSelect.value = branchData.id;
                            const distance = parseFloat(branchData.distance_km).toFixed(1);
                            finalStatus += `Nearest branch: ${branchData.name} (${distance} km away).`;
                        }

                        statusText.textContent = finalStatus;
                    })
                    .catch(error => {
                        statusText.textContent = 'An error occurred while fetching location details.';
                        console.error('Error during location fetch:', error);
                    });

            }, function() {
                statusText.textContent = 'Unable to retrieve your location. Please grant permission.';
            });
        });
        <?php endif; ?>
    });
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>