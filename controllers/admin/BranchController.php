<?php
require_once __DIR__ . '/../../models/Branch.php';

class BranchController {
    public function index() {
        // Include session and auth
        require_once __DIR__ . '/../../includes/session.php';
        require_once __DIR__ . '/../auth.php';
        
        Session::start();
        if (!Session::isLoggedIn() || !in_array(Session::get('user_role'), ['admin', 'manager'])) {
            header("Location: ../views/login_logout_modules/login.php");
            exit();
        }
        
        $branchModel = new Branch();
        
        $feedback = '';
        $edit_branch = null;

        // Handle POST request (create/update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'name' => trim($_POST['name']),
                'address' => trim($_POST['address']),
                'phone' => trim($_POST['phone'])
            ];
            
            if ($id) { // Update
                if ($branchModel->update($id, $data)) {
                    $feedback = '<div class="alert alert-success">Branch updated!</div>';
                } else {
                    $feedback = '<div class="alert alert-danger">Error updating.</div>';
                }
            } else { // Create
                if ($branchModel->create($data)) {
                    $feedback = '<div class="alert alert-success">Branch added!</div>';
                } else {
                    $feedback = '<div class="alert alert-danger">Error adding.</div>';
                }
            }
        }

        // Handle GET request (delete/edit form)
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            $id = $_GET['id'] ?? null;

            if ($action === 'delete' && $id) {
                if ($branchModel->delete($id)) {
                    $feedback = '<div class="alert alert-success">Branch deleted!</div>';
                } else {
                    $feedback = '<div class="alert alert-danger">Error deleting branch.</div>';
                }
            }
            if ($action === 'edit' && $id) {
                $edit_branch = $branchModel->findById($id);
            }
        }
        
        $branches = $branchModel->getAll();
        $page_title = 'Branch Management';
        require_once __DIR__ . '/../../views/admin/branches/index.php';
    }


}