<?php
// /app/SecurityLogger.php

namespace App;

use PDO;
use PDOException;

class SecurityLogger
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * The logging method, with the same name as your original private method.
     */
    public function logEvent(string $level, string $eventType, array $context = []): void
    {
        try {
            $sql = "INSERT INTO security_logs (user_id, ip_address, level, event_type, message) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $userId = $context['user_id'] ?? null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $message = json_encode($context);
            $stmt->execute([$userId, $ipAddress, $level, $eventType, $message]);
        } catch (PDOException $e) {
            // In a real application, this should log to a file as a fallback.
            error_log("CRITICAL: DB Logger Failed: " . $e->getMessage());
        }
    }
}