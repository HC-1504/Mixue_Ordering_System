<?php
namespace App\Auth;

interface AuthenticatorInterface {
    public function login(string $email, string $password): bool;
    public function getLoggedInUser(): ?object;
}