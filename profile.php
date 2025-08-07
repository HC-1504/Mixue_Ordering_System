<?php
// C:\xampp\htdocs\Assignment\profile.php
//111111
// 1. SETUP THE ENVIRONMENT: Include the service factory first.
// This creates the $loginService and $authManager variables in the global scope.
require_once __DIR__ . '/controllers/auth.php'; 

// 2. INCLUDE THE CONTROLLER CLASS
require_once __DIR__ . '/controllers/ProfileController.php';

// 3. START THE SESSION (can be done in auth.php or here)
Session::start();

// 4. CREATE THE CONTROLLER AND CALL THE METHOD
$controller = new ProfileController();
$controller->displayPage(); // This will now work