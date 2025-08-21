<?php
// Set a unique title for this page
$page_title = 'Reset Password - Mixue System';
$body_class = 'login-page';

// Include necessary dependencies first
require_once(__DIR__ . '/../../includes/config.php');
require_once(__DIR__ . '/../../includes/session.php');

// Start the session
Session::start();

// Include the auth controller to get access to $authManager
require_once(__DIR__ . '/../../controllers/auth.php');

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
if (!$token) {
    // A simple way to handle a critical error
    $page_title = 'Error - Reset Password';
    require_once(__DIR__ . '/../../includes/header.php');
    echo "<div class='login-container error'>Error: No password reset token provided.</div>";
    require_once(__DIR__ . '/../../includes/footer.php');
    exit();
}

$errors = [];
// --- PHP Logic for completing the reset ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $errors = $authManager->completePasswordReset(
            $_POST['token'] ?? '',
            $_POST['password'] ?? '',
            $_POST['confirm_password'] ?? ''
        );
        if (empty($errors)) {
            Session::destroy();
            Session::set('success_message', 'Your password has been reset successfully. Please log in.');
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
            exit();
        }
    }
}

// Generate a new CSRF token for the form
$csrf_token = Session::generateCsrfToken();

// Include header AFTER all potential redirects
require_once(__DIR__ . '/../../includes/header.php');
?>

<!-- The HTML content is wrapped for proper layout -->
<div class="login-container">
    <h2>Choose a New Password</h2>
    <p style="margin-top: -15px; margin-bottom: 25px; color: #666;">Your new password must be secure.</p>

    <?php if (!empty($errors)): ?>
        <div class="message-box error" style="text-align: left;">
            <?php foreach ($errors as $error): ?>
                <p style="margin: 0.5rem 0;"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="login-form" action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
        <input type="hidden" name="_csrf" value="<?= $csrf_token ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        
        <p>
            <label for="password">New Password</label>
            <span class="password-guide" style="display:block; color:#888; font-size:13px; margin-bottom:4px;">
                Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a number, and a special symbol.
            </span>
            <div style="position:relative;">
                <input type="password" id="password" name="password" required placeholder="••••••••" autocomplete="new-password" style="padding-right:32px;">
                <span onclick="togglePassword('password', this)" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                </span>
            </div>
        </p>
        <p>
            <label for="confirm_password">Confirm New Password</label>
            <div style="position:relative;">
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••" autocomplete="new-password" style="padding-right:32px;">
                <span onclick="togglePassword('confirm_password', this)" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                </span>
            </div>
        </p>
        
        <button type="submit" class="login-button">Set New Password</button>
    </form>
<script>
function togglePassword(fieldId, iconSpan) {
    var input = document.getElementById(fieldId);
    var icon = iconSpan.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>

    <div class="login-links">
        <a href="<?= BASE_URL ?>/views/login_logout_modules/login.php">Back to Login</a>
    </div>
</div>

<?php 
// Use the main website footer
require_once(__DIR__ . '/../../includes/footer.php'); 
?>
