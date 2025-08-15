<?php
// 1. Include the controller that creates your service objects
require_once '../controllers/auth.php'; // <-- THIS IS THE MISSING LINE

// 2. Check if the user is logged in
if (!Session::isLoggedIn()) {
    header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
    exit();
}

// 3. Include the header AFTER all potential redirects
require_once '../includes/header.php';

$user = $authManager->findUserById(Session::get('user_id'));

$success = '';
$error = '';

// Soft check for reloads table
try {
    $conn = Database::getInstance();
    $conn->query('SELECT 1 FROM reloads LIMIT 1');
} catch (PDOException $e) {
    echo '<div class="alert alert-warning text-center">The <b>reloads</b> table does not exist. Please run:<br><code>CREATE TABLE reloads (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, amount DECIMAL(10,2) NOT NULL, created_at DATETIME NOT NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);</code></div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
        $amount = floatval($_POST['amount'] ?? 0);
        if ($amount <= 0) {
            $error = 'Please enter a valid positive amount.';
        } else {
            // Update balance in DB
            $conn = Database::getInstance();
            $stmt = $conn->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
            if ($stmt->execute([$amount, $user->id])) {
                // Record the reload transaction
                $stmt2 = $conn->prepare('INSERT INTO reloads (user_id, amount, created_at) VALUES (?, ?, NOW())');
                $stmt2->execute([$user->id, $amount]);
                $success = 'Money reloaded successfully!';
                // Refresh user data
                $user = $authManager->findUserById($user->id);
            } else {
                $error = 'Failed to reload money. Please try again.';
            }
        }
    } else {
        $error = 'Invalid security token. Please try again.';
    }
}
?>
<div class="container" style="max-width: 500px; margin: 2rem auto;">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4"><i class="fas fa-wallet"></i> Reload Money</h3>
            <div class="mb-3 text-center">
                <span style="font-size:1.2rem; color:#888;">Current Balance:</span><br>
                <span style="font-size:2rem; font-weight:700; color:#28a745;">RM <?= number_format($user->balance ?? 0, 2) ?></span>
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"> <?= htmlspecialchars($success) ?> </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger text-center"> <?= htmlspecialchars($error) ?> </div>
            <?php endif; ?>
            <form method="POST" action="reload.php">
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
                <a href="/Assignment/profile.php" class="btn btn-secondary w-100 mt-2">Back to Profile</a>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?> 