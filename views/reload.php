<?php
// views/reload.php - THE NEW, CLEAN "VIEW" VERSION

// The header is included here, but all logic is handled by the controller before this file is loaded.
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 2rem auto;">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4"><i class="fas fa-wallet"></i> Reload Money</h3>
            
            <div class="mb-3 text-center">
                <span style="font-size:1.2rem; color:#888;">Current Balance:</span><br>
                <!-- The $user variable is now provided by your ProfileController -->
                <span style="font-size:2rem; font-weight:700; color:#28a745;">RM <?= number_format($user->balance ?? 0, 2) ?></span>
            </div>

            <!-- Display success or error messages passed from the ProfileController -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <?= htmlspecialchars($error) ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- The form action now correctly points to the root entry point (the router) -->
            <form method="POST" action="<?= BASE_URL ?>/reload.php">
                <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
                
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount to Reload (RM)</label>
                    <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                </div>
                
                <div class="mb-3">
                    <label for="payment_type" class="form-label">Payment Type</label>
                    <select class="form-select" id="payment_type" name="payment_type" required>
                        <option value="" disabled selected>Select payment type</option>
                        <option value="TNG eWallet">TNG eWallet</option>
                        <option value="GrabPay">GrabPay</option>
                        <option value="Online Banking">Online Banking</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus-circle"></i> Reload</button>
                
                <!-- This link should point back to the main profile page entry point -->
                <a href="<?= BASE_URL ?>/profile.php" class="btn btn-secondary w-100 mt-2">Back to Profile</a>
            </form>
        </div>
    </div>
</div>

<?php
// Include the main website footer
require_once __DIR__ . '/../includes/footer.php'; 
?>