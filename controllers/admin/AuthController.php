<?php
// This controller handles admin-specific auth actions like profile update

class AuthController {
    public function profile() {
        // --- CHANGE 1: Include the new service factory file ---
        // This file creates the $authManager object for us to use.
        // The path should be relative to this AuthController.php file.
        require_once __DIR__ . '/../controllers/auth.php';
        
        // --- CHANGE 2: The old line "$auth = new Auth();" is DELETED ---

        // CHANGE 3: Use the new $authManager object instead of the old $auth object.
        $user = $authManager->findUserById(Session::get('user_id'));

        // Handle POST request for password change
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
                // CHANGE 4: Use the new $authManager object here as well.
                $changeErrors = $authManager->changePassword(
                    $user->id,
                    $_POST['current_password'] ?? '',
                    $_POST['new_password'] ?? '',
                    $_POST['confirm_new_password'] ?? ''
                );
                if (empty($changeErrors)) {
                    Session::set('profile_success', 'Password changed successfully.');
                } else {
                    Session::set('profile_errors', $changeErrors);
                }
            }
            // No changes needed below this line in the controller logic
            header('Location: profile.php');
            exit();
        }
        
        // Get session messages for the view
        $errors = Session::get('profile_errors', []); Session::unset('profile_errors');
        $success = Session::get('profile_success'); Session::unset('profile_success');
        
        $page_title = 'Admin Profile';
        // The path to the view remains the same
        require_once __DIR__ . '/../../views/admin/profile.php';
    }
}