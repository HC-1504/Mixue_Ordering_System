<?php
// Security: Store credentials securely (e.g., .env file), not hardcoded.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'mixue_db');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                // Security: Use generic error messages. Log detailed error internally.
                error_log('DB Connection Error: ' . $e->getMessage());
                die('A system error occurred. Please contact support.');
            }
        }
        return self::$instance;
    }
}