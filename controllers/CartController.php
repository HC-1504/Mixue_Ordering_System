<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/Assignment');
}

class CartController
{
    public function index()
    {
        Session::start();

        $cart = Cart::get();
        $productModel = new Product();

        $cartItems = [];
        $total = 0;

        foreach ($cart as $key => $item) {
            if (is_object($item)) $item = (array) $item;

            $product = $productModel->findById($item['id']);
            if (!$product) continue;

            if (is_object($product)) $product = (array) $product;

            $subtotal = $product['price'] * $item['quantity']; // ✅ array access
            $total += $subtotal;

            $cartItems[] = [
                'index' => $key,
                'product' => $product,
                'quantity' => $item['quantity'],
                'temperature' => $item['temperature'],
                'sugar' => $item['sugar'],
                'subtotal' => $subtotal,
            ];
        }

        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function add()
    {
        Session::start();

        if (!isset($_POST['_csrf']) || !Session::verifyCsrfToken($_POST['_csrf'])) {
            // CSRF token is invalid, handle the error
            // For example, show an error message or redirect
            die('CSRF token validation failed.');
        }

        $id = $_POST['id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        $temperature = $_POST['temperature'] ?? '';
        $sugar = $_POST['sugar'] ?? '';

        if ($id > 0) {
            $productModel = new Product();
            $product = $productModel->findById($id);
            $name = $product ? $product->name : '';

            Cart::add([
                'id' => $id,
                'quantity' => $quantity,
                'temperature' => $temperature,
                'sugar' => $sugar,
            ]);

            header('Location: ' . BASE_URL . '/routes/menu.php?added=' . urlencode($name));
            exit;
        }

        header('Location: ' . BASE_URL . '/routes/menu.php');
        exit;
    }

    public function update()
    {
        Session::start();

        $index = $_POST['index'] ?? null;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
        $temperature = $_POST['temperature'] ?? '';
        $sugar = $_POST['sugar'] ?? '';

        if ($index !== null) {
            Cart::update($index, [
                'quantity' => $quantity,
                'temperature' => $temperature,
                'sugar' => $sugar
            ]);
        }

        header('Location: ' . BASE_URL . '/routes/cart.php');
        exit;
    }

    public function remove($index)
    {
        Cart::remove($index);
        header('Location: ' . BASE_URL . '/routes/cart.php');
        exit;
    }

    public function removeAll()
    {
        Cart::clear();
        header('Location: ' . BASE_URL . '/routes/cart.php');
        exit;
    }
}
