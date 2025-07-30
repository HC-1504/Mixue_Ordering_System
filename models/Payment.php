<?php
class Payment
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
    }

    public function record($orderId, $amount, $method)
    {
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, amount, payment_date, payment_method) VALUES (?, ?, NOW(), ?)");
        return $stmt->execute([$orderId, $amount, $method]);
    }
}
