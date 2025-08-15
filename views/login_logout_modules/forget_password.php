<?php
// Set a unique title for this page
$page_title = 'Forgot Password - Mixue System';
$body_class = 'login-page'; // <-- DEFINE THE CLASS HERE, BEFORE THE HEADER

// Include necessary dependencies first
require_once '../../includes/config.php';
require_once '../../includes/session.php';

// Include the auth controller to get access to $authManager
require_once '../../controllers/auth.php';

// If user is already logged in, they shouldn't be here.
if (Session::isLoggedIn()) {
    header('Location: ' . BASE_URL . '/profile.php');
    exit();
}

// Include header AFTER all potential redirects
require_once '../../includes/header.php'; // <-- NOW THE HEADER CAN USE THE VARIABLE

$message = null;
$error = null;

// --- PHP Logic for password reset request (no changes needed) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
        if (empty($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            $authManager->requestPasswordReset($email);
            $message = 'If an account with that email exists, a password reset link has been sent. Please check your inbox.';
        }
    }
}
?>

<!-- The HTML content is wrapped for proper layout -->
<div class="login-container">
    <h2>Forgot Password?</h2>
    <p style="margin-top: -15px; margin-bottom: 25px; color: #666;">Enter your email and we'll send you a reset link.</p>

    <?php if ($error): ?><div class="message-box error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($message): ?><div class="message-box success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <form class="login-form" action="" method="POST">
        <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
        <p>
            <label for="email">Your Email Address</label>
            <input type="email" id="email" name="email" required placeholder="you@example.com">
        </p>
        <button type="submit" class="login-button">Send Reset Link</button>
    </form>

    <div class="login-links">
        <a href="<?= BASE_URL ?>/views/login_logout_modules/login.php">Back to Login</a>
    </div>
</div>

<?php 
// Use the main website footer
require_once '../../includes/footer.php'; 
?>