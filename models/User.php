<?php
class User
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
    }

    public function getBalance($userId)
    {
        $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_OBJ)?->balance ?? 0;
    }

    public function deductBalance($userId, $amount)
    {
        $stmt = $this->conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        return $stmt->execute([$amount, $userId]);
    }

    public function addBalance($userId, $amount)
    {
        $stmt = $this->conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        return $stmt->execute([$amount, $userId]);
    }
}
