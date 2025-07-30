<?php
// actions/logout.php

// We need our factory file because it contains the logout() function.
require_once __DIR__ . '/../controllers/auth.php';

// Call the global logout function.
logout();

// The logout function already handles redirecting the user, but we can add one
// here as a fallback in case that ever changes.
header('Location: ' . BASE_URL . '/index.php');
exit();