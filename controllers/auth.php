<?php
// controllers/auth.php

// --- BOOTSTRAP APPLICATION & SERVICES ---

// 1. Load Composer's autoloader (which loads all classes from /app)
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Load dependencies that are not yet using namespaces
require_once __DIR__ . '/../includes/db.php';
// If you have a session helper, include it here. e.g.,
// require_once __DIR__ . '/../includes/session.php'; 

// 3. Import all the new classes we created
use App\SecurityLogger;
use App\AuthService;
use App\Auth\DatabaseAuthenticator;
use App\Auth\AccountLockoutDecorator;
use App\Auth\SessionLoginDecorator;

// --- SMTP Configuration (can stay here for now or move to a config file) ---
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'testinnnnggg@gmail.com');
define('SMTP_PASS', 'lluajtwcvqzhmzkn');
define('SMTP_PORT', 587);

// --- SERVICE FACTORY ---
// This block creates the objects your pages will use. These variables are now
// available to any script that `require`s this file.

$pdo = Database::getInstance();
$logger = new SecurityLogger($pdo);

/**
 * @var AuthService $authManager Handles registration, password changes, user management, etc.
 */
$authManager = new AuthService($pdo, $logger);

/**
 * @var \App\Auth\AuthenticatorInterface $loginService Handles the LOGIN process using the Decorator Pattern.
 */
$loginService = new SessionLoginDecorator( // 3rd decorator (handles session and success logging)
    new AccountLockoutDecorator(         // 2nd decorator (handles lockout and failure logging)
        new DatabaseAuthenticator($pdo), // 1st is the core component that just checks the password
        $pdo,
        $logger
    ),
    $logger
);

/**
 * A simple logout function for convenience.
 */
function logout() {
    global $logger; // Use the logger we just created above
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $logger->logEvent('INFO', 'LOGOUT_SUCCESS', ['user_id' => $userId]);
    }
    session_unset();
    session_destroy();
    // Redirect to login page after destroying the session
    header('Location: login.php'); // Adjust path if needed
    exit();
}