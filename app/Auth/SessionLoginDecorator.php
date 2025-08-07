<?php
// /app/Auth/SessionLoginDecorator.php - CORRECTED

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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_role'] = $user->role;
            $this->logger->logEvent('INFO', 'LOGIN_SUCCESS', ['user_id' => $user->id, 'role' => $user->role]);
        }
        
        return $isSuccess;
    }

    public function getLoggedInUser(): ?object
    {
        return $this->authenticator->getLoggedInUser();
    }

    /**
     * --- THIS IS THE FIX ---
     * This magic method catches calls to methods that don't exist in this class
     * (like findUserById) and forwards them to the wrapped authenticator object.
     * This makes the decorator "transparent" for all other methods.
     *
     * @param string $name The name of the method being called.
     * @param array $arguments The arguments passed to the method.
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->authenticator->$name(...$arguments);
    }
}