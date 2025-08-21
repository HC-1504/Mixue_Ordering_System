<?php
require_once __DIR__ . '/../includes/db.php';

class Reload
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }
    public function addReload($userId, $amount, $paymentMethodType)
    {
        $stmt = $this->pdo->prepare('INSERT INTO reloads (user_id, amount, payment_type, created_at) VALUES (?, ?, ?, NOW())');
        $result = $stmt->execute([$userId, $amount, $paymentMethodType]);
        if ($result) {
            // Insert into security_logs
            $logStmt = $this->pdo->prepare('INSERT INTO security_logs (user_id, ip_address, level, event_type, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $msg = json_encode([
                'amount' => $amount,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            $logStmt->execute([$userId, $ip, 'INFO', 'RELOAD', $msg]);
        }
        return $result;
    }
    public function getReloadsByUser($userId)
    {
        $sql = 'SELECT * FROM reloads WHERE user_id = ? ORDER BY created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        // Print the select SQL and parameters for debugging
        // echo "[DEBUG] getReloadsByUser SQL: $sql | user_id=$userId<br>";
        return $stmt->fetchAll();
    }
}
