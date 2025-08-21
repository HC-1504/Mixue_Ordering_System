<?php
// includes/header.php - UPDATED WITH PREVIOUS PATH STRUCTURE

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Assuming config.php is in the same directory (e.g., /includes/)
require_once __DIR__ . '/config.php'; 
require_once __DIR__ . '/session.php';

$isLoggedIn = Session::isLoggedIn();
$userRole = Session::get('user_role');

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Mixue System' ?></title>
    
    <!-- CSS Links -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        header.sticky-header {
            position: sticky;
            top: 0;
            z-index: 1050;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s cubic-bezier(.4, 0, .2, 1), box-shadow 0.3s;
        }

        header.sticky-header.hide {
            transform: translateY(-100%);
            box-shadow: none;
        }

        header.sticky-header.show {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        /* Hamburger styles */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            width: 40px;
            height: 40px;
            cursor: pointer;
            z-index: 1100;
        }

        .hamburger span {
            height: 4px;
            width: 28px;
            background: #333;
            margin: 4px 0;
            border-radius: 2px;
            transition: 0.3s;
        }

        @media (max-width: 900px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem 2rem;
            }

            .nav-links.open {
                display: flex;
            }

            .hamburger {
                display: flex;
            }
        }

        .btn-profile {
            background: #2563eb;
            color: #fff !important;
            font-weight: bold;
            border-radius: 6px;
            padding: 8px 18px;
            margin-left: 8px;
            transition: background 0.2s;
        }

        .btn-profile:hover {
            background: #1741a6;
            color: #fff !important;
        }

        .btn-logout {
            background: #e11d48;
            color: #fff !important;
            font-weight: bold;
            border-radius: 6px;
            padding: 8px 18px;
            margin-left: 8px;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background: #a30d2d;
            color: #fff !important;
        }

        .btn-login-signup {
            color: #fff !important;
            font-weight: bold;
        }
    </style>
</head>

<body class="<?= isset($body_class) ? htmlspecialchars($body_class) : '' ?>">
    <header class="sticky-header show">
        <div class="navbar">
            <a href="<?= BASE_URL ?>../views/index.php" class="nav-logo">
                <img src="<?= BASE_URL ?>/assets/images/mixue-logo.png" alt="Mixue Logo">
            </a>
            <div class="hamburger" id="hamburger-menu" aria-label="Open navigation" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links">
                <li><a href="<?= BASE_URL ?>../views/index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>../views/about.php">About Us</a></li>
                <li><a href="<?= BASE_URL ?>/routes/menu.php">Menu</a></li>
                <?php if (Session::isLoggedIn()): ?>
                    <li style="position:relative;">
                        <a href="<?= BASE_URL ?>/routes/cart.php">
                            Cart
                            <?php if ($cart_count > 0): ?>
                                <span style="position: absolute; top: -8px; right: -18px; background: red; color: white; border-radius: 50%; padding: 2px 7px; font-size: 12px; font-weight: bold;">
                                    <?= $cart_count ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li><a href="<?= BASE_URL ?>../views/locations.php">Locations</a></li>
                    <li style="margin-left:auto;"></li>
                    <li><a href="<?= BASE_URL ?>/profile.php" class="btn-profile">My Profile</a></li>
                    <li><a href="<?= BASE_URL ?>/views/login_logout_modules/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>../views/locations.php">Locations</a></li>
                    <li style="margin-left:auto;"></li>
                    <li><a href="<?= BASE_URL ?>/views/login_logout_modules/login.php" class="btn btn-primary btn-login-signup">Login</a></li>
                    <li><a href="<?= BASE_URL ?>/views/login_logout_modules/register.php" class="btn btn-primary btn-login-signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    <main>
        <script>
            // Sticky header show/hide on scroll up/down
            (function() {
                let lastScroll = 0;
                const header = document.querySelector('header.sticky-header');
                window.addEventListener('scroll', function() {
                    const currentScroll = window.pageYOffset;
                    if (currentScroll <= 0) {
                        header.classList.remove('hide');
                        header.classList.add('show');
                        return;
                    }
                    if (currentScroll > lastScroll) {
                        // Scrolling down
                        header.classList.remove('show');
                        header.classList.add('hide');
                    } else {
                        // Scrolling up
                        header.classList.remove('hide');
                        header.classList.add('show');
                    }
                    lastScroll = currentScroll;
                });
            })();
            // Hamburger menu toggle
            document.addEventListener('DOMContentLoaded', function() {
                const hamburger = document.getElementById('hamburger-menu');
                const navLinks = document.querySelector('.nav-links');
                hamburger.addEventListener('click', function() {
                    navLinks.classList.toggle('open');
                });
                hamburger.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        navLinks.classList.toggle('open');
                    }
                });
            });
        </script>