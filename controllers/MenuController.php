<?php
// controllers/MenuController.php

require_once __DIR__ . '/../models/Product.php';

class MenuController
{
    /**
     * Display the menu page with all available products
     */
    public function index()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            // Initialize product model
            $productModel = new Product();

            // Get all available products with their categories
            $products = $productModel->getAvailableWithCategory();

            // Check if user is logged in
            $is_logged_in = isset($_SESSION['user_id']);

            // Get any success message from query parameters
            $success_message = $_GET['added'] ?? null;

            // Load the view with data
            require __DIR__ . '/../views/menu/index.php';
        } catch (Exception $e) {
            // Log the error and show a user-friendly message
            error_log('MenuController Error: ' . $e->getMessage());

            // You might want to redirect to an error page or show a message
            $_SESSION['error'] = 'An error occurred while loading the menu. Please try again later.';
            header('Location: /');
            exit();
        }
    }
}
