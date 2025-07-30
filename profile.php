<?php
// C:\xampp\htdocs\Assignment\profile.php

// Start the session at the very top.
session_start();

// Include the controller class definition.
require_once __DIR__ . '/controllers/ProfileController.php';

// Create an instance of the controller.
$controller = new ProfileController();

// Tell the controller to handle displaying the profile page.
$controller->displayPage();