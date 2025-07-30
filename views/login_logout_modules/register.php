<?php
// views/login_logout_modules/register.php - THE CORRECTED VERSION

// Include necessary files first
require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/db.php';

// --- CHANGE 1: We include our factory file here ---
require_once '../../controllers/auth.php';

$page_title = 'Create Account - Mixue System';
$body_class = 'login-page';

Session::start();
// --- CHANGE 2: The line "$auth = new Auth();" has been DELETED ---

if (Session::isLoggedIn()) {
    header('Location: ' . BASE_URL . '/profile.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
        $errors[] = 'Invalid security token. Please try submitting the form again.';
    } else {
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        // --- CHANGE 3: Use the new $authManager object ---
        // It was created for us inside controllers/auth.php
        $errors = $authManager->registerUser($name, $email, $password, $confirm);
        
        if (empty($errors)) {
            Session::set('success_message', 'Registration successful! You can now log in.');
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
            exit();
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="login-container">
    <h2>Create Your Account</h2>
    <p style="margin-top: -15px; margin-bottom: 25px; color: #666;">It's quick and easy.</p>

    <?php if (!empty($errors)): ?>
        <div class="message-box error" style="text-align: left;">
            <?php foreach ($errors as $error): ?>
                <p style="margin: 0.5rem 0;"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="login-form" action="register.php" method="POST">
        <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
        
        <p>
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required placeholder="e.g. John Doe" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </p>
        <p>
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </p>
        <p>
            <label for="password">Password</label>
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
            <label for="confirm_password">Confirm Password</label>
            <div style="position:relative;">
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••" autocomplete="new-password" style="padding-right:32px;">
                <span onclick="togglePassword('confirm_password', this)" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                </span>
            </div>
        </p>
        
        <button type="submit" class="login-button">Sign Up</button>
    </form>

    <div class="login-links">
        <span>Already have an account?</span>
        <a href="<?= BASE_URL ?>/views/login_logout_modules/login.php">Sign In</a>
    </div>
</div>

<?php 
require_once '../../includes/footer.php'; 
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