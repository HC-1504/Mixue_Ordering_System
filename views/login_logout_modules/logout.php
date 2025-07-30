<?php
// 1. Include your configuration and session files first
require_once '../../includes/config.php';
require_once '../../includes/session.php';

// 2. Start the session so it can be accessed and destroyed
Session::start();

// 3. Destroy the entire session to log the user out. This is the correct method.
Session::destroy();

// 4. Redirect the user to the login page using the full, reliable BASE_URL
header('Location: ' . BASE_URL . '/views/login_logout_modules/login.php');
exit();