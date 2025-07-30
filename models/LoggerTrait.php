<?php
require_once __DIR__ . '/../includes/session.php';

trait LoggerTrait {
    /**
     * Logs a security-related event to the database.
     *
     * @param string $level 'INFO', 'WARN', or 'CRITICAL'
     * @param string $eventType A descriptive event type (e.g., 'PRODUCT_CREATE')
     * @param array $context Additional data to be stored in the message field
     */
    protected function logEvent(string $level, string $eventType, array $context = []): void {
        try {
            // The class using this trait must have a public or protected $pdo property.
            if (!isset($this->pdo) || !$this->pdo instanceof PDO) {
                // Fail silently if PDO is not available to avoid breaking the main functionality.
                error_log("LoggerTrait Error: PDO property not available in " . get_class($this));
                return;
            }

            $sql = "INSERT INTO security_logs (user_id, ip_address, level, event_type, message) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            // Get the admin user ID from the current session
            $userId = Session::get('user_id'); 
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            
            // Add user_id to the context for a more complete log message
            $context['admin_user_id'] = $userId;
            $message = json_encode($context); 
            
            $stmt->execute([$userId, $ipAddress, $level, $eventType, $message]);

        } catch (Exception $e) {
            // If the logger itself fails, log to the server's error log instead of crashing the app.
            error_log("CRITICAL: The security logger failed to write to the database. Error: " . $e->getMessage());
        }
    }
}