<?php
// /app/AuthService.php

namespace App;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use DateTime;
use PDOException;

class AuthService
{
    private PDO $pdo;
    private SecurityLogger $logger;
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_TIME = '15 minutes';

    const PASSWORD_HISTORY_LIMIT = 5;

    public function __construct(PDO $pdo, SecurityLogger $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function registerUser(string $name, string $email, string $password, string $confirm): array
    {
        $errors = [];
        if (empty($name) || empty($email) || empty($password)) {
            $errors[] = "All fields are required.";
        }
        if ($password !== $confirm) {
            $errors[] = "Passwords do not match.";
        }
        if (!$this->isPasswordComplex($password)) {
            $errors[] = "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special symbol.";
        }
        if ($this->findUserByEmail($email)) {
            $errors[] = "An account with this email address already exists.";
        }

        if (!empty($errors)) {
            $this->logger->logEvent('WARN', 'REGISTER_FAIL_VALIDATION', ['email' => $email, 'reason' => implode(', ', $errors)]);
            return $errors;
        }

        try {
            $hash = password_hash($password, PASSWORD_ARGON2ID);
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $hash]);
            $userId = $this->pdo->lastInsertId();

            $this->addPasswordToHistory($userId, $hash);
            $this->logger->logEvent('INFO', 'REGISTER_SUCCESS', ['user_id' => $userId]);
            return [];
        } catch (PDOException $e) {
            $this->logger->logEvent('CRITICAL', 'REGISTER_DB_ERROR', ['error' => $e->getMessage()]);
            return ['A system error occurred during registration. Please try again later.'];
        }
    }
    
    public function requestPasswordReset(string $email): void
    {
        $user = $this->findUserByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $hash = hash('sha256', $token);
            date_default_timezone_set('Asia/Kuala_Lumpur');
            $expires = (new DateTime('+' . self::LOCKOUT_TIME))->format('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user->id, $hash, $expires]);
            
            $this->sendPasswordResetEmail($user->email, $user->name, $token);
            
            $this->logger->logEvent('INFO', 'PASS_RESET_REQUEST', ['user_id' => $user->id]);
        } else {
            $this->logger->logEvent('WARN', 'PASS_RESET_FAIL_NO_USER', ['email' => $email]);
        }
    }

    public function completePasswordReset(string $token, string $password, string $confirm): array
    {
        $resetRecord = $this->findResetRecordByToken($token);
        if (!$resetRecord || new DateTime() > new DateTime($resetRecord->expires_at)) {
            if ($resetRecord) $this->deleteResetToken($resetRecord->id);
            $this->logger->logEvent('WARN', 'PASS_RESET_FAIL_INVALID_TOKEN');
            return ['Invalid or expired password reset link. Please request a new one.'];
        }

        $errors = [];
        if ($password !== $confirm) $errors[] = "The new passwords do not match.";
        if (!$this->isPasswordComplex($password)) $errors[] = "The new password does not meet complexity requirements.";
        if ($this->isPasswordInHistory($resetRecord->user_id, $password)) $errors[] = "You cannot reuse one of your last " . self::PASSWORD_HISTORY_LIMIT . " passwords.";
        
        if (!empty($errors)) {
            $this->logger->logEvent('WARN', 'PASS_RESET_FAIL_VALIDATION', ['user_id' => $resetRecord->user_id, 'reason' => implode(', ', $errors)]);
            return $errors;
        }

        $this->updateUserPassword($resetRecord->user_id, $password);
        $this->deleteResetToken($resetRecord->id);
        $this->logger->logEvent('INFO', 'PASS_RESET_SUCCESS', ['user_id' => $resetRecord->user_id]);
        return [];
    }
    
    public function changePassword(int $userId, string $current, string $new, string $confirm): array
    {
        $user = $this->findUserById($userId);
        if (!$user || !password_verify($current, $user->password)) {
            $this->logger->logEvent('WARN', 'PASS_CHANGE_FAIL_WRONG_CURRENT', ['user_id' => $userId]);
            return ['Your current password is incorrect.'];
        }
        if ($new !== $confirm) return ['The new passwords do not match.'];
        if (!$this->isPasswordComplex($new)) return ['The new password does not meet complexity requirements.'];
        if ($this->isPasswordInHistory($userId, $new)) return ['You cannot reuse one of your last ' . self::PASSWORD_HISTORY_LIMIT . " passwords."];
        
        $this->updateUserPassword($userId, $new);
        $this->logger->logEvent('INFO', 'PASS_CHANGE_SUCCESS', ['user_id' => $userId]);
        return [];
    }

    public function changeUserRole(int $userId, string $newRole, int $managerId): bool
    {
        $allowedRoles = ['user', 'admin', 'manager'];
        if (!in_array($newRole, $allowedRoles)) {
            $this->logger->logEvent('WARN', 'ROLE_CHANGE_INVALID_ROLE', ['user_id' => $userId, 'manager_id' => $managerId, 'attempted_role' => $newRole]);
            return false;
        }

        $user = $this->findUserById($userId);
        if (!$user) {
            $this->logger->logEvent('WARN', 'ROLE_CHANGE_USER_NOT_FOUND', ['user_id' => $userId, 'manager_id' => $managerId]);
            return false;
        }

        $oldRole = $user->role;
        if ($oldRole === $newRole) {
            return true; // No change needed
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $result = $stmt->execute([$newRole, $userId]);
            
            if ($result) {
                $this->logger->logEvent('INFO', 'ROLE_CHANGE_SUCCESS', ['user_id' => $userId, 'manager_id' => $managerId, 'old_role' => $oldRole, 'new_role' => $newRole, 'user_email' => $user->email]);
                return true;
            } else {
                $this->logger->logEvent('ERROR', 'ROLE_CHANGE_DB_FAIL', ['user_id' => $userId, 'manager_id' => $managerId]);
                return false;
            }
        } catch (PDOException $e) {
            $this->logger->logEvent('CRITICAL', 'ROLE_CHANGE_DB_ERROR', ['user_id' => $userId, 'manager_id' => $managerId, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function findUserById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    // --- Private Helper Methods ---

    private function findUserByEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    private function sendPasswordResetEmail(string $userEmail, string $userName, string $token): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->setFrom('testinnnnggg@gmail.com', 'Mixue System');
            $mail->addAddress($userEmail, $userName);
            $mail->isHTML(true);
            $mail->Subject = 'Mixue System - Password Reset Request';
            $baseUrl = defined('BASE_URL') ? BASE_URL : '/Assignment';
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . $baseUrl . "/views/login_logout_modules/reset_password.php?token=" . urlencode($token);
            $mail->Body    = "<div style='font-family: sans-serif; line-height: 1.6;'><h2>Password Reset Request</h2><p>Hello " . htmlspecialchars($userName) . ",</p><p>We received a request to reset the password for your account. Please click the button below to choose a new password. This link will expire in 15 minutes.</p><p style='text-align: center;'><a href='" . $resetLink . "' style='display: inline-block; padding: 12px 24px; font-size: 16px; color: white; background-color: #007bff; text-decoration: none; border-radius: 5px;'>Reset Your Password</a></p><p>If you did not request a password reset, you can safely ignore this email. Your account is secure.</p><p>Thank you,<br>The Mixue System Team</p></div>";
            $mail->AltBody = "Hello " . htmlspecialchars($userName) . ",\n\nPlease use the following link to reset your password. This link is valid for 15 minutes.\n\n" . $resetLink . "\n\nIf you did not request this, please ignore this email.";
            $mail->send();
            $this->logger->logEvent('INFO', 'EMAIL_SEND_SUCCESS', ['email' => $userEmail]);
            return true;
        } catch (Exception $e) {
            $this->logger->logEvent('CRITICAL', 'EMAIL_SEND_FAIL', ['email' => $userEmail, 'error' => $mail->ErrorInfo]);
            return false;
        }
    }

    private function isPasswordComplex(string $password): bool
    {
        if (strlen($password) < 8) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        if (!preg_match('/[\W_]/', $password)) return false;
        return true;
    }

    private function isPasswordInHistory(int $userId, string $newPassword): bool
    {
        $stmt = $this->pdo->prepare("SELECT password_hash FROM password_history WHERE user_id = ? ORDER BY created_at DESC LIMIT " . self::PASSWORD_HISTORY_LIMIT);
        $stmt->execute([$userId]);
        $history = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($history as $hash) {
            if (password_verify($newPassword, $hash)) return true;
        }
        return false;
    }

    private function addPasswordToHistory(int $userId, string $hash): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO password_history (user_id, password_hash) VALUES (?, ?)");
        $stmt->execute([$userId, $hash]);
    }

    private function updateUserPassword(int $userId, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $userId]);
        $this->addPasswordToHistory($userId, $hash);
    }

    private function findResetRecordByToken(string $token)
    {
        $hash = hash('sha256', $token);
        $stmt = $this->pdo->prepare("SELECT * FROM password_resets WHERE token_hash = ?");
        $stmt->execute([$hash]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    private function deleteResetToken(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE id = ?");
        $stmt->execute([$id]);
    }
}