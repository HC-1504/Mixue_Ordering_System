<?php
require_once __DIR__ . '/../models/Product.php';

header('Content-Type: application/json');

try {
    $productModel = new Product();
    $products = $productModel->getMenuWithCategoryApi();
    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while fetching the menu.']);
}
