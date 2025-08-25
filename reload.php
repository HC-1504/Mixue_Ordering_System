<?php
// C:\xampp\htdocs\Assignment\reload.php

// This is the new page the user will visit to reload money.

session_start();
require_once __DIR__ . '/controllers/ProfileController.php';

$controller = new ProfileController();
// We'll create this new method in the controller.
$controller->handleReloadPage();