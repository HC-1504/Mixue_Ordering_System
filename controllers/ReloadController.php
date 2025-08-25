<?php
require_once __DIR__ . '/../models/Reload.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../controllers/auth.php';

class ReloadController {
    private $reloadModel;
    private $pdo;
    public function __construct() {
        $this->reloadModel = new Reload();
        $this->pdo = Database::getInstance();
    }
    public function reloadPage() {
        global $authManager; // Access the global $authManager from auth.php

        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
            exit();
        }
        $user = $authManager->findUserById(Session::get('user_id'));
        $success = $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Session::verifyCsrfToken($_POST['_csrf'] ?? '')) {
                $amount = floatval($_POST['amount'] ?? 0);
                if ($amount <= 0) {
                    $error = 'Please enter a valid positive amount.';
                } else {
                    $stmt = $this->pdo->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
                    if ($stmt->execute([$amount, $user->id])) {
                        $this->reloadModel->addReload($user->id, $amount);
                        $success = 'Money reloaded successfully!';
                        $user = $authManager->findUserById($user->id);
                    } else {
                        $error = 'Failed to reload money. Please try again.';
                    }
                }
            } else {
                $error = 'Invalid security token. Please try again.';
            }
        }
        $reloads = $this->reloadModel->getReloadsByUser($user->id);
        require __DIR__ . '/../views/reload.php';
    }
} 