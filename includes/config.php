<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/Assignment');
}

// Stripe API Keys
define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY']);
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY']);
