<?php
require_once __DIR__ . '/../models/OrderDetail.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../includes/session.php'; // Ensure session is available

class OrderDetailController
{
    public function show($id)
    {
        ob_start();
        $this->getOrderDetailsApi($id);
        $jsonResponse = ob_get_clean();
        $data = json_decode($jsonResponse, true);

        if (isset($data['error'])) {
            if (http_response_code() === 404) {
                require __DIR__ . '/../views/order/not_found.php';
            } else {
                die($data['error']);
            }
            return;
        }

        $order = $data['order'];
        $details = $data['details'];

        // Consume the menu API to get product details
        $menuJson = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../api/menu.php');
        $menuItems = json_decode($menuJson, true);
        $menuMap = array_column($menuItems, null, 'id');

        // Combine order details with menu details
        $fullDetails = [];
        foreach ($details as $item) {
            $productId = $item['product_id'];
            if (isset($menuMap[$productId])) {
                $product = $menuMap[$productId];
                $fullDetails[] = array_merge($item, [
                    'product_name' => $product['name'],
                    'unit_price' => $product['price'],
                    'product_image' => $product['image']
                ]);
            }
        }
        $details = $fullDetails;

        // Calculate subtotal
        $subtotal = 0;
        foreach ($details as $item) {
            $subtotal += $item['unit_price'] * $item['quantity'];
        }

        require __DIR__ . '/../views/order/order_details.php';
    }

    public function getOrderDetailsApi($id)
    {
        Session::start();

        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Access Denied: You must be logged in to view orders.']);
            return;
        }

        $orderModel = new Order();
        $detailModel = new OrderDetail();

        $order = $orderModel->find($id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found.']);
            return;
        }

        $details = $detailModel->getOrderDetailsByOrderId($id);

        $response = [
            'order' => $order,
            'details' => $details
        ];

        http_response_code(200);
        echo json_encode($response);
    }
}
