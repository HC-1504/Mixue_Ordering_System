<?php
// Security: A single, site-wide component for session management.
class Session {

    //secure cookie configurations
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 1800, 
                'httponly' => true, 
                'samesite' => 'Lax', 
                'secure' => isset($_SERVER['HTTPS'])
            ]);
            session_start();
        }
    }

    public static function set($key, $value) { $_SESSION[$key] = $value; }
    public static function get($key, $default = null) { return $_SESSION[$key] ?? $default; }
    public static function unset($key) { unset($_SESSION[$key]); }


    //session validation and management
    public static function destroy() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time()-42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function regenerate() { 
        session_regenerate_id(true); 
    }

    public static function isLoggedIn(): bool { return isset($_SESSION['user_id']); }

    public static function generateCsrfToken(): string {
        $token = bin2hex(random_bytes(32));
        self::set('_csrf', $token);
        return $token;
    }

    public static function verifyCsrfToken(string $token): bool {
        return hash_equals(self::get('_csrf', ''), $token);
    }
}