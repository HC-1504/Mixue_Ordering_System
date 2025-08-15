<?php
// controllers/ProfileController.php - THE FINAL, COMPLETE VERSION

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Reload.php';
require_once __DIR__ . '/../includes/db.php';
// From your original reload.php
require_once __DIR__ . '/auth.php'; // <-- This is correct
//11111
class ProfileController
{
    /**
     * The constructor is empty and does not cause any errors.
     */
    public function __construct()
    {
    }

    /**
     * Method 1: Displays the main profile page.
     * THIS IS THE METHOD THAT WAS MISSING.
     */
    public function displayPage()
    {
        // Start the session
        Session::start();
        
        // Include auth.php to get access to the services
        require_once __DIR__ . '/auth.php';

        // Access the global $loginService variable (and $authManager if needed elsewhere)
        global $loginService, $authManager; // Note we are using $loginService

        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
            exit();
        }

        $user_id = Session::get('user_id');
        
        // --- THIS IS THE KEY FIX ---
        // Use the correct object: $authManager, not $loginService
        $user = $authManager->findUserById($user_id);

        if (!$user) {
            // Use the global logout function from auth.php
            logout();
            exit();
        }

        // The rest of your code remains the same
        $reloadModel = new Reload();
        $reloads = $reloadModel->getReloadsByUser($user_id);
        
        $conn = Database::getInstance();
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        $order_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $errors = Session::get('profile_errors', []); Session::unset('profile_errors');
        $success = Session::get('profile_success'); Session::unset('profile_success');

        require_once __DIR__ . '/../views/profile.php';
    }

    /**
     * Method 2: Displays the change password form.
     */
    public function displayChangePasswordPage()
    {
        require_once __DIR__ . '/auth.php';

        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
            exit();
        }
        
        $errors = Session::get('profile_errors', []); Session::unset('profile_errors');
        $success = Session::get('profile_success'); Session::unset('profile_success');
        
        $page_title = 'Change Password';
        require_once __DIR__ . '/../views/change_password.php';
    }

    /**
     * Method 3: Handles the password change form submission.
     */
    public function handlePasswordChange()
    {
        // Include our "factory" file to get access to the $authManager.
        require_once __DIR__ . '/auth.php';

        // Access the global $authManager variable
        global $authManager;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::isLoggedIn() || !Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
            logout();
            exit();
        }
        
        $user_id = Session::get('user_id');

        $changeErrors = $authManager->changePassword(
            $user_id,
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? '',
            $_POST['confirm_new_password'] ?? ''
        );

        if (empty($changeErrors)) {
            Session::set('profile_success', 'Your password was changed successfully.');
        } else {
            Session::set('profile_errors', $changeErrors);
        }

        // Redirect back to the change password page to show the result.
        header('Location: ' . BASE_URL . '/change_password.php');
        exit();
    }

    public function handleReloadPage() {
    // 1. Security Check: User must be logged in
    if (!Session::isLoggedIn()) {
        header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
        exit();
    }

    // 2. Include auth.php to get access to the services
    require_once __DIR__ . '/auth.php';
    
    // 3. Access the global $authManager variable
    global $authManager;
    
    // 4. Setup variables
    $user = $authManager->findUserById(Session::get('user_id'));
    $success = '';
    $errors = [];

    // 5. Process the form if it was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
            $amount = floatval($_POST['amount'] ?? 0);
            $payment_type = $_POST['payment_type'] ?? '';
            
            if ($amount <= 0) {
                $errors[] = 'Please enter a valid positive amount.';
            } 
            if (empty($payment_type)) {
                $errors[] = 'Please select a payment type.';
            }

            if (empty($errors)) {
                // Update balance in DB
                $conn = Database::getInstance();
                $stmt = $conn->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
                if ($stmt->execute([$amount, $user->id])) {
                    // Record the reload transaction
                    $stmt2 = $conn->prepare('INSERT INTO reloads (user_id, amount, payment_type, created_at) VALUES (?, ?, ?, NOW())');
                    $stmt2->execute([$user->id, $amount, $payment_type]);
                    
                    $success = 'Money reloaded successfully!';
                    // Refresh user data to show the new balance immediately
                    $user = $authManager->findUserById($user->id); 
                } else {
                    $errors[] = 'Failed to reload money. Please try again.';
                }
            }
        } else {
            $errors[] = 'Invalid security token. Please try again.';
        }
    }

    // 6. Set page title and body class for the view
    $page_title = 'Reload Money - Mixue System';
    $body_class = 'reload-page';

    // 7. Load the view and pass the prepared data to it
    require_once __DIR__ . '/../views/reload.php';
}
}