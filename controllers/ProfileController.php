<?php
// controllers/ProfileController.php - THE FINAL, COMPLETE VERSION

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Reload.php';
require_once __DIR__ . '/../includes/db.php';

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
        // DO NOT require_once 'auth.php' here anymore. It's done in profile.php.

        // Access the global $loginService variable (and $authManager if needed elsewhere)
        global $loginService, $authManager; // Note we are using $loginService

        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
            exit();
        }

        $user_id = Session::get('user_id');
        
        // --- THIS IS THE KEY FIX ---
        // Use the correct object: $loginService, not $authManager
        $user = $loginService->findUserById($user_id);

        if (!$user) {
            logout(); // The global logout() function from auth.php will work
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
}