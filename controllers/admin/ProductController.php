<?php
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';

class ProductController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index() {
        // Get search and filter parameters
        $search = $_GET['search'] ?? '';
        $category_filter = $_GET['category'] ?? '';
        $status_filter = $_GET['status'] ?? '';
        
        // Get filtered products
        $products = $this->productModel->getAllWithFilters($search, $category_filter, $status_filter);
        
        // Get all categories for filter dropdown
        $categories = $this->categoryModel->getAll();
        
        $page_title = 'Product Management';
        require_once __DIR__ . '/../../views/admin/products/index.php';
    }

    public function create() {
        $categories = $this->categoryModel->getAll();
        $page_title = 'Add New Product';
        $product = null; // No product data for create form
        require_once __DIR__ . '/../../views/admin/products/form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id'],
                'is_available' => isset($_POST['is_available']) ? 1 : 0
            ];
            
            if ($this->productModel->create($data, $_FILES['image'])) {
                Session::set('feedback', 'Product created successfully!');
            } else {
                Session::set('feedback', 'Error creating product.');
            }
        }
        header('Location: products.php');
        exit();
    }

    public function edit($id) {
        $product = $this->productModel->findById($id);
        $categories = $this->categoryModel->getAll();
        $page_title = 'Edit Product';
        require_once __DIR__ . '/../../views/admin/products/form.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id'],
                'is_available' => isset($_POST['is_available']) ? 1 : 0
            ];

            if ($this->productModel->update($id, $data, $_FILES['image'])) {
                Session::set('feedback', 'Product updated successfully!');
            } else {
                Session::set('feedback', 'Error updating product.');
            }
        }
        header('Location: products.php');
        exit();
    }

    public function delete($id) {
        if ($this->productModel->delete($id)) {
            Session::set('feedback', 'Product deleted successfully!');
        } else {
            Session::set('feedback', 'Error deleting product.');
        }
        header('Location: products.php');
        exit();
    }
}