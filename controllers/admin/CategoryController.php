<?php
require_once __DIR__ . '/../../models/Category.php';

class CategoryController {
    public function index() {
        $categoryModel = new Category();
        
        $feedback = '';
        $edit_category = null;

        // Handle POST request (create/update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = trim($_POST['name']);
            if ($id) { // Update
                if ($categoryModel->update($id, $name)) {
                    $feedback = '<div class="alert alert-success">Category updated!</div>';
                } else {
                    $feedback = '<div class="alert alert-danger">Error updating.</div>';
                }
            } else { // Create
                if ($categoryModel->create($name)) {
                    $feedback = '<div class="alert alert-success">Category added!</div>';
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
                if ($categoryModel->delete($id)) {
                    $feedback = '<div class="alert alert-success">Category deleted!</div>';
                } else {
                    $feedback = '<div class="alert alert-danger">Cannot delete. Category might have products linked.</div>';
                }
            }
            if ($action === 'edit' && $id) {
                $edit_category = $categoryModel->findById($id);
            }
        }
        
        $categories = $categoryModel->getAll();
        $page_title = 'Category Management';
        require_once __DIR__ . '/../../views/admin/categories/index.php';
    }
}