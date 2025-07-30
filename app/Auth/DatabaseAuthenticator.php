<?php
namespace App\Auth;

use PDO;

class DatabaseAuthenticator implements AuthenticatorInterface {
    private PDO $pdo;
    private ?object $user = null;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function login(string $email, string $password): bool {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user && password_verify($password, $user->password)) {
            $this->user = $user;
            return true;
        }
        return false;
    }

    public function getLoggedInUser(): ?object { return $this->user; }
}