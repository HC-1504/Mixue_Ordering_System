<?php
// /app/Auth/SessionLoginDecorator.php

namespace App\Auth;

use App\SecurityLogger;

class SessionLoginDecorator implements AuthenticatorInterface
{
    private AuthenticatorInterface $authenticator;
    private SecurityLogger $logger;

    public function __construct(AuthenticatorInterface $authenticator, SecurityLogger $logger)
    {
        $this->authenticator = $authenticator;
        $this->logger = $logger;
    }

    public function login(string $email, string $password): bool
    {
        $isSuccess = $this->authenticator->login($email, $password);

        if ($isSuccess) {
            $user = $this->getLoggedInUser();
            // Start the session if it's not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_role'] = $user->role;

            // --- THE FIX IS HERE ---
            // Changed from ->log() to ->logEvent() to match the SecurityLogger class
            $this->logger->logEvent('INFO', 'LOGIN_SUCCESS', ['user_id' => $user->id, 'role' => $user->role]);
        }
        
        return $isSuccess;
    }

    public function getLoggedInUser(): ?object
    {
        return $this->authenticator->getLoggedInUser();
    }
}