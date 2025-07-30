<?php
// C:\xampp\htdocs\Assignment\change_password.php

// This is the page the user will visit to see the form.

session_start();
require_once __DIR__ . '/controllers/ProfileController.php';

$controller = new ProfileController();
// This new method will display the form.
$controller->displayChangePasswordPage();