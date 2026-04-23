<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Your Online Shopping Destination</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ezycommerce/Customer/css/index.css">
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="top-header-text">
                <span>Free shipping on orders over $50 | 30-day money-back guarantee</span>
            </div>
            <div class="top-header-links">
                <a href="#"><i class="fas fa-phone"></i> +1 (555) 123-4567</a>
                <a href="#"><i class="fas fa-envelope"></i> support@shopease.com</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header>
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <a href="#" class="logo">
                        <i class="fas fa-shopping-bag"></i>
                        ShopEase
                    </a>
                    
                    <div class="search-bar">
                        <input type="text" placeholder="Search for products...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="header-actions">
                        <div class="header-action user-menu">
                            <div class="user-avatar">JS</div>
                            <span>John Smith</span>
                        </div>
                        <div class="header-action">
                            <i class="far fa-heart"></i>
                            <span class="badge">3</span>
                        </div>
                        <div class="header-action">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge">2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation with Categories -->
        <nav>
            <div class="container">
                <ul class="nav-links" id="categories-nav">
                    <!-- Categories will be loaded here via AJAX -->
                    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                    <div class="loading">
                        <div class="spinner"></div>
                    </div>
                </ul>
            </div>
        </nav>
    </header>

