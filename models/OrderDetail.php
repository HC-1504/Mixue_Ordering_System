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
            SELECT od.product_id, od.quantity, od.temperature, od.sugar
            FROM order_details od
            WHERE od.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
