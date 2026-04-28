<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user']) && isset($_SESSION['user']['id']);
$username = $isLoggedIn ? ($_SESSION['user']['username'] ?? 'Account') : 'Guest';
$avatarText = strtoupper(substr($username, 0, 2));
$homeUrl = url('/');
$cartUrl = url('/cart');
$wishlistUrl = url('/wishlist');
$profileUrl = url('/profile');
$loginUrl = url('/login');
$logoutUrl = url('/logout');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Your Online Shopping Destination</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('/Customer/css/index.css'); ?>">
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="top-header-text">
                <span>Free shipping on orders over $50 | 30-day money-back guarantee</span>
            </div>
            <div class="top-header-links">
                <a href="tel:+15551234567"><i class="fas fa-phone"></i> +1 (555) 123-4567</a>
                <a href="mailto:support@shopease.com"><i class="fas fa-envelope"></i> support@shopease.com</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header>
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <a href="<?php echo $homeUrl; ?>" class="logo">
                        <i class="fas fa-shopping-bag"></i>
                        ShopEase
                    </a>
                    
                    <div class="search-bar">
                        <input type="text" placeholder="Search for products...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="header-actions">
                        <?php if ($isLoggedIn): ?>
                            <a href="<?php echo $profileUrl; ?>" class="header-action user-menu">
                                <div class="user-avatar"><?php echo htmlspecialchars($avatarText); ?></div>
                                <span><?php echo htmlspecialchars($username); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $loginUrl; ?>" class="header-action user-menu">
                                <div class="user-avatar">GN</div>
                                <span>Login</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo $isLoggedIn ? $wishlistUrl : $loginUrl; ?>" class="header-action" aria-label="Wishlist">
                            <i class="far fa-heart"></i>
                            <span class="badge" id="wishlist-count">0</span>
                        </a>
                        <a href="<?php echo $isLoggedIn ? $cartUrl : $loginUrl; ?>" class="header-action" aria-label="Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge" id="cart-count">0</span>
                        </a>
                        <?php if ($isLoggedIn): ?>
                            <a href="<?php echo $logoutUrl; ?>" class="header-action">Logout</a>
                        <?php else: ?>
                            <a href="<?php echo $loginUrl; ?>" class="header-action">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation with Categories -->
        <nav>
            <div class="container">
                <ul class="nav-links" id="categories-nav">
                    <!-- Categories will be loaded here via AJAX -->
                    <li><a href="<?php echo $homeUrl; ?>"><i class="fas fa-home"></i> Home</a></li>
                </ul>
            </div>
        </nav>
    </header>

