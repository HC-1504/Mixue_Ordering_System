<?php
// C:\xampp\htdocs\Assignment\actions\change_password.php

// Start the session.
session_start();

// Include the controller class definition.
require_once __DIR__ . '/../controllers/ProfileController.php';

// Create an instance of the controller.
$controller = new ProfileController();

// Tell the controller to specifically handle the password change logic.
$controller->handlePasswordChange();