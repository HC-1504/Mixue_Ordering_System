<?php
require_once __DIR__ . '/../includes/db.php';

class OrderDetail
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
    }

    public function getOrderDetailsByOrderId($orderId)
    {
        $stmt = $this->conn->prepare("
            SELECT od.*, p.name AS product_name, p.price AS unit_price
            FROM order_details od
            JOIN products p ON od.product_id = p.id
            WHERE od.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
