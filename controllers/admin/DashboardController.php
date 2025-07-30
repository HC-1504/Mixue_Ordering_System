<?php
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../models/Branch.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../controllers/auth.php';

class DashboardController {
    public function index() {
        $productModel = new Product();
        $categoryModel = new Category();
        $branchModel = new Branch();
        $orderModel = new Order();

        $data['product_count'] = $productModel->getCount();
        $data['category_count'] = $categoryModel->getCount();
        $data['branch_count'] = $branchModel->getCount();
        $data['order_count'] = $orderModel->getCount();
        
        // Get recent orders
        $data['recent_orders'] = $orderModel->getRecentOrders(5);

        // Load the view
        $page_title = 'Admin Dashboard';
        require_once __DIR__ . '/../../views/admin/dashboard.php';
    }
}