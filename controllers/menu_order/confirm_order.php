<?php
define('BASE_URL', '/Assignment');
session_start();
require_once '../../includes/db.php';

// Get PDO connection
$conn = Database::getInstance();

// Get user_id from session
$user_id = $_SESSION['user_id'] ?? null;

// Detect order type from URL (?type=delivery or ?type=pickup)
$type = $_GET['type'] ?? 'pickup'; // Default to pickup

// Get cart from session
$cart = $_SESSION['cart'] ?? [];

// Fetch branches for pickup location
$branches = [];
if ($type === 'pickup') {
    $stmt = $conn->query("SELECT id, name FROM branches");
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Redirect if cart is empty
if (empty($cart)) {
    header('Location:' . BASE_URL . '/views/cart.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone   = $_POST['phone'] ?? '';
    $address = '';
    $branch_id = null;
    if ($type === 'delivery') {
        $address = $_POST['address'] ?? '';
    } elseif ($type === 'pickup') {
        $branch_id = $_POST['branch_id'] ?? null;
    }

    $total = 0;

    // Calculate total price using PDO
    foreach ($cart as $item) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([intval($item['id'])]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $total += $row['price'] * $item['quantity'];
        }
    }

    // Save order data in session (instead of DB)
    $_SESSION['pending_order'] = [
        'user_id' => $user_id,
        'phone' => $phone,
        'address' => $address,
        'type' => $type,
        'branch_id' => $branch_id,
        'total' => $total
    ];

    // Redirect to payment page
    header('Location:' . BASE_URL . '/views/payment.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Confirm Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h1 class="mb-4 text-center">🧾 Confirm Your Order</h1>

        <form method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>

            <?php if ($type === 'delivery'): ?>
                <div class="mb-3">
                    <label for="address" class="form-label">Delivery Address</label>
                    <textarea name="address" id="address" class="form-control" required></textarea>
                </div>
            <?php elseif ($type === 'pickup'): ?>
                <div class="mb-3">
                    <label for="branch_id" class="form-label">Pickup Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        <option value="">Select a branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= htmlspecialchars($branch['id']) ?>"><?= htmlspecialchars($branch['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100">Place Order</button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/views/cart.php" class="btn btn-secondary">← Back to Cart</a>
        </div>
    </div>

</body>

</html>