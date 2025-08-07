<?php
// app/Auth/DatabaseAuthenticator.php - CORRECTED

namespace App\Auth;

use PDO;
//lllllll
class DatabaseAuthenticator implements AuthenticatorInterface 
{
    private PDO $pdo;
    private ?object $user = null;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    public function login(string $email, string $password): bool 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user && password_verify($password, $user->password)) {
            $this->user = $user;
            return true;
        }
        return false;
    }

    public function getLoggedInUser(): ?object 
    {
        return $this->user;
    }

    /**
     * --- THIS IS THE FIX ---
     * Add the missing method to find a user by their ID.
     * This is the final destination for the call from ProfileController.
     *
     * @param int $id The ID of the user to find.
     * @return object|null The user object if found, otherwise null.
     */
    public function findUserById(int $id): ?object 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        
        // fetch() returns false if no row is found, so we cast to null for consistency.
        return $user ?: null; 
    }
}