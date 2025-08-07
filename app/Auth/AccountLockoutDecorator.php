<?php
// /app/Auth/AccountLockoutDecorator.php

namespace App\Auth;

use PDO;
use DateTime;
use App\SecurityLogger;

class AccountLockoutDecorator implements AuthenticatorInterface
{
    private AuthenticatorInterface $authenticator;
    private PDO $pdo;
    private SecurityLogger $logger;

    public function __call($name, $arguments)
    {
        // This line forwards the call to the next object in the chain
        // (which is likely your DatabaseAuthenticator)
        return $this->authenticator->$name(...$arguments);
    }

    public function __construct(AuthenticatorInterface $authenticator, PDO $pdo, SecurityLogger $logger)
    {
        $this->authenticator = $authenticator;
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->findUserByEmail($email);

        if ($user && $this->isAccountLocked($user)) {
            // --- FIX 1 ---
            $this->logger->logEvent('WARN', 'LOGIN_FAIL_LOCKED', ['user_id' => $user->id]);
            return false;
        }

        $isSuccess = $this->authenticator->login($email, $password);

        if ($user) {
            if ($isSuccess) {
                $this->resetFailedAttempts($user->id);
            } else {
                $this->recordFailedAttempt($user->id);
                // --- FIX 2 ---
                $this->logger->logEvent('WARN', 'LOGIN_FAIL_WRONG_PASS', ['user_id' => $user->id]);
            }
        } elseif (!$isSuccess) { // Only log "no user" if the login actually failed
             // --- FIX 3 ---
            $this->logger->logEvent('WARN', 'LOGIN_FAIL_NO_USER', ['email' => $email]);
        }
        
        return $isSuccess;
    }

    public function getLoggedInUser(): ?object { return $this->authenticator->getLoggedInUser(); }
    
    // Helper methods moved from your original Auth class
    private function findUserByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
    private function isAccountLocked(object $user): bool {
        return $user->account_locked_until && new DateTime() < new DateTime($user->account_locked_until);
    }
    private function resetFailedAttempts(int $userId): void {
        $this->pdo->prepare("UPDATE users SET failed_login_attempts = 0, account_locked_until = NULL WHERE id = ?")->execute([$userId]);
    }
    private function recordFailedAttempt(int $userId): void {
        $this->pdo->prepare("UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE id = ?")->execute([$userId]);
        $user = $this->pdo->query("SELECT * FROM users WHERE id = $userId")->fetch(\PDO::FETCH_OBJ);
        if ($user && $user->failed_login_attempts >= 5) {
            $lockoutTime = (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
            $this->pdo->prepare("UPDATE users SET account_locked_until = ? WHERE id = ?")->execute([$lockoutTime, $userId]);
            // --- FIX 4 ---
            $this->logger->logEvent('CRITICAL', 'ACCOUNT_LOCKED', ['user_id' => $userId]);
        }
    }
}