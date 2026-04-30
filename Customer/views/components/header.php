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

// $minimalHeader can be set before including this file to render a compact header
// suitable for dashboard/profile pages (no search, no category nav, no promo banner)
$minimalHeader = $minimalHeader ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'EzyCommerce — Your Online Shopping Destination'; ?></title>
    <meta name="description" content="<?php echo $pageDescription ?? 'Discover premium products at EzyCommerce. Quality meets modern design with free shipping on orders over $50.'; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('/Customer/css/index.css'); ?>">
    <?php if (!empty($extraCss)): ?>
        <?php foreach ((array)$extraCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body<?php echo !empty($bodyClass) ? ' class="' . $bodyClass . '"' : ''; ?>>
    <?php if (!$minimalHeader): ?>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="top-header-text">
                <span><i class="fas fa-truck"></i> Free shipping on orders over $50 | 30-day money-back guarantee</span>
            </div>
            <div class="top-header-links">
                <a href="tel:+15551234567"><i class="fas fa-phone"></i> +1 (555) 123-4567</a>
                <a href="mailto:support@ezycommerce.com"><i class="fas fa-envelope"></i> support@ezycommerce.com</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Header -->
    <header>
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <a href="<?php echo $homeUrl; ?>" class="logo">
                        <i class="fas fa-shopping-bag"></i>
                        EzyCommerce
                    </a>
                    
                    <?php if (!$minimalHeader): ?>
                    <div class="search-bar">
                        <input type="text" placeholder="Search for products, brands and more...">
                        <button aria-label="Search"><i class="fas fa-search"></i></button>
                    </div>
                    <?php endif; ?>
                    
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
                        <?php if (!$minimalHeader): ?>
                        <a href="<?php echo $isLoggedIn ? $wishlistUrl : $loginUrl; ?>" class="header-action" aria-label="Wishlist">
                            <i class="far fa-heart"></i>
                            <span class="badge" id="wishlist-count">0</span>
                        </a>
                        <a href="<?php echo $isLoggedIn ? $cartUrl : $loginUrl; ?>" class="header-action" aria-label="Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge" id="cart-count">0</span>
                        </a>
                        <?php endif; ?>
                        <?php if ($isLoggedIn): ?>
                            <a href="<?php echo $logoutUrl; ?>" class="header-action">Logout</a>
                        <?php elseif (!$minimalHeader): ?>
                            <a href="<?php echo $loginUrl; ?>" class="header-action">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!$minimalHeader): ?>
        <!-- Navigation with Categories -->
        <nav>
            <div class="container">
                <ul class="nav-links" id="categories-nav">
                    <!-- Categories will be loaded here via AJAX -->
                    <li><a href="<?php echo $homeUrl; ?>"><i class="fas fa-home"></i> Home</a></li>
                </ul>
            </div>
        </nav>
        <?php endif; ?>
    </header>

