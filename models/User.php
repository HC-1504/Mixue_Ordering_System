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
        $user = $this->find($userId);
        if (!$user) {
            return 'user_not_found';
        }
        if ($user->balance < $amount) {
            return 'insufficient_balance';
        }

        $stmt = $this->conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        return $stmt->execute([$amount, $userId]) ? 'success' : 'db_error';
    }

    public function updateBalance($userId, $amount)
    {
        $stmt = $this->conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        return $stmt->execute([$amount, $userId]);
    }

    public function find($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

}
