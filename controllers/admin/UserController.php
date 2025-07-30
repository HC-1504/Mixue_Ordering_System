<?php
// admin/UserController.php - THE NEW, CORRECTED VERSION

// We no longer need to require auth.php at the top level.
// We will include it inside the method that needs the services.
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/session.php';

class UserController
{
    private PDO $pdo;
    // --- CHANGE 1: The old "$auth" property is REMOVED ---
    // private Auth $auth; 
    
    public function __construct()
    {
        // --- CHANGE 2: The constructor now ONLY initializes the PDO connection ---
        $this->pdo = Database::getInstance();
        // The line "$this->auth = new Auth();" has been DELETED.
    }
    
    public function index()
    {
        // This method does not use the Auth class, so it needs no changes.
        // It correctly checks permissions and uses the local helper method to get users.
        if (Session::get('user_role') !== 'manager') {
            Session::set('user_errors', ['You do not have permission to access user management.']);
            header('Location: ../admin/dashboard.php');
            exit();
        }
        
        $users = $this->getAllUsers();
        $success = Session::get('user_success');
        Session::unset('user_success');
        $errors = Session::get('user_errors');
        Session::unset('user_errors');
        
        $page_title = 'User Management';
        require_once __DIR__ . '/../../views/admin/users/index.php';
    }
    
    public function changeRole()
    {
        // --- CHANGE 3: Include our "factory" file here to get $authManager ---
        require_once __DIR__ . '/../auth.php';

        // Access the global $authManager variable
        global $authManager;

        // All the permission and validation checks are still perfectly fine.
        if (Session::get('user_role') !== 'manager') {
            Session::set('user_errors', ['You do not have permission to change user roles.']);
            header('Location: users.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
            Session::set('user_errors', ['Invalid request.']);
            header('Location: users.php');
            exit();
        }
        
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $newRole = trim($_POST['new_role'] ?? '');
        
        if (!$userId || !in_array($newRole, ['user', 'admin', 'manager'])) {
            Session::set('user_errors', ['Invalid user ID or role.']);
            header('Location: users.php');
            exit();
        }
        
        if ($userId == Session::get('user_id')) {
            Session::set('user_errors', ['You cannot change your own role.']);
            header('Location: users.php');
            exit();
        }
        
        try {
            $managerId = Session::get('user_id');
            // --- CHANGE 4: Use the new $authManager object instead of $this->auth ---
            $result = $authManager->changeUserRole($userId, $newRole, $managerId);
            
            if ($result) {
                $user = $this->getUserById($userId);
                Session::set('user_success', "Successfully changed {$user->name}'s role to {$newRole}.");
            } else {
                Session::set('user_errors', ['Failed to update user role.']);
            }
        } catch (Exception $e) {
            Session::set('user_errors', ['An error occurred while updating the user role.']);
            error_log('Role change error: ' . $e->getMessage());
        }
        
        header('Location: users.php');
        exit();
    }
    
    // These private helper methods are fine as they only use the PDO connection.
    private function getAllUsers()
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, role, failed_login_attempts, account_locked_until, created_at FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getUserById($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}