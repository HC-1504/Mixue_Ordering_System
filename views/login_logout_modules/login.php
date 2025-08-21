<?php
// views/login_logout_modules/login.php - THE CORRECTED VERSION

// --- CHANGE 1: We now include the main config file first ---
require_once(__DIR__ . '/../../includes/config.php');
require_once(__DIR__ . '/../../includes/session.php');

Session::start();

// --- CHANGE 2: We include our factory file, but DO NOT create a new Auth() object ---
require_once(__DIR__ . '/../../controllers/auth.php');
// The line "$auth = new Auth();" has been DELETED.

$page_title = 'Login - Mixue System';
$body_class = 'login-page';

$error = null;
$success_message = Session::get('success_message');
Session::unset('success_message');

if (Session::isLoggedIn()) {
    $userRole = Session::get('user_role');
    $redirect_path = in_array($userRole, ['admin', 'manager']) ? '/admin/dashboard.php' : '/profile.php';
    header('Location: ' . BASE_URL . $redirect_path);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
        $password = $_POST['password'] ?? '';
        $post_redirect = $_POST['redirect'] ?? (BASE_URL . '/profile.php');

        // --- CHANGE 3: Use the new $loginService object ---
        // It was created for us inside controllers/auth.php
        if ($loginService->login($email, $password)) {
            $userRole = Session::get('user_role');
            if (in_array($userRole, ['admin', 'manager'])) {
                header('Location: ' . BASE_URL . '/admin/dashboard.php');
            } else {
                header('Location: ' . $post_redirect);
            }
            exit();
        } else {
            // The decorators handle logging the failure, so we just set the error message.
            $error = 'The email or password you entered is incorrect, or the account is locked.';
        }
    }
}

require_once(__DIR__ . '/../../includes/header.php');
?>

<div class="auth-content-wrapper">
    <div class="login-container">
        <h2>Welcome Back!</h2>

        <?php if ($error): ?><div class="message-box error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success_message): ?><div class="message-box success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>

        <form class="login-form" action="login.php" method="POST">
            <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? (BASE_URL . '/profile.php')) ?>">
            
            <p>
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com" autocomplete="email">
            </p>
            <p>
                <label for="password">Password</label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" required placeholder="••••••••" autocomplete="current-password">
                    <span onclick="togglePassword('password', this)" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); cursor:pointer;">
                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                    </span>
                    </div>
            </p>

            
            <button type="submit" class="login-button">Sign In</button>
        </form>

        <div class="login-links">
            <a href="<?= BASE_URL ?>/views/login_logout_modules/forget_password.php">Forgot password?</a>
            <span class="separator">|</span>
            <a href="<?= BASE_URL ?>/views/login_logout_modules/register.php">Create an account</a>
        </div>
    </div>
</div>

<?php 
require_once(__DIR__ . '/../../includes/footer.php'); 
?>

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