<?php
define('BASE_URL', '/Assignment');
$page_title = $page_title ?? 'Mixue Admin Panel';

// Include the auth controller to get access to $authManager
// Use a static variable to ensure we only include it once
static $authIncluded = false;
if (!$authIncluded) {
    require_once __DIR__ . '/../../controllers/auth.php';
    $authIncluded = true;
}

// Access the global $authManager variable
global $authManager;

$currentUser = $authManager->findUserById(Session::get('user_id'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .admin-header{background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);padding:1rem 0;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .admin-header .navbar-brand{color:#fff !important;font-weight:700;text-decoration:none;display:flex;align-items:center}
        .admin-header .navbar-brand img{height:40px;margin-right:10px}
        .admin-header .nav-link{color:rgba(255,255,255,0.9) !important;padding:.5rem 1rem;border-radius:5px;transition:all .3s ease;text-decoration:none;cursor:pointer !important;pointer-events:auto !important}
        .admin-header .nav-link:hover{color:#fff !important;background-color:rgba(255,255,255,0.1)}
        .admin-header .nav-link.active{background-color:rgba(255,255,255,0.2);color:#fff !important}
        .admin-actions{display:flex;align-items:center;gap:1rem}
        .admin-actions .btn{border-radius:20px;padding:.5rem 1rem}
        .admin-actions .btn.active{background-color:rgba(255,255,255,0.3) !important;border-color:rgba(255,255,255,0.3) !important}
        body{padding-top:0;}
        
        /* Debug styles */
        .nav-link {
            cursor: pointer !important;
            pointer-events: auto !important;
            z-index: 1000 !important;
            position: relative !important;
        }
        
        /* Ensure links are clickable */
        a.nav-link {
            display: inline-block !important;
            text-decoration: none !important;
        }
        
        /* Debug hover effect */
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1) !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <a class="navbar-brand" href="dashboard.php">
                    <img src="<?= BASE_URL ?>/assets/images/mixue-logo.png" alt="Mixue Logo">
                </a>
                
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <ul class="nav d-flex">
                    <li class="nav-item"><a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php" onclick="console.log('Dashboard clicked')"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($current_page == 'orders.php') ? 'active' : '' ?>" href="orders.php" onclick="console.log('Orders clicked')"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <?php if (Session::get('user_role') === 'manager'): ?>
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'users.php') ? 'active' : '' ?>" href="users.php" onclick="console.log('Users clicked')"><i class="fas fa-users"></i> Users</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'reports.php') ? 'active' : '' ?>" href="reports.php" onclick="console.log('Reports clicked')"><i class="fas fa-chart-line"></i> Reports</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link <?= ($current_page == 'products.php') ? 'active' : '' ?>" href="products.php" onclick="console.log('Products clicked')"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($current_page == 'categories.php') ? 'active' : '' ?>" href="categories.php" onclick="console.log('Categories clicked')"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($current_page == 'branches.php') ? 'active' : '' ?>" href="branches.php" onclick="console.log('Branches clicked')"><i class="fas fa-store"></i> Branches</a></li>
                </ul>

                <div class="admin-actions">
                    <a href="profile.php" class="btn btn-light btn-sm <?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                        <i class="fas fa-user-shield"></i> <?= htmlspecialchars($currentUser->name) ?>
                    </a>
                    <a href="<?= BASE_URL ?>/views/login_logout_modules/logout.php" class="btn btn-light btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main class="container-fluid mt-4">