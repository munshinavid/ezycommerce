<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Your One-Stop E-Commerce Destination</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --white: #ffffff;
            --black: #000000;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header Styles */
        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .top-bar {
            background: var(--primary);
            color: var(--white);
            padding: 8px 0;
            font-size: 0.9rem;
        }

        .top-bar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .top-bar-links a {
            color: var(--white);
            text-decoration: none;
            margin-left: 15px;
            transition: opacity 0.3s;
        }

        .top-bar-links a:hover {
            opacity: 0.8;
        }

        .main-header {
            padding: 15px 0;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--dark);
        }

        .logo-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-right: 10px;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .search-bar {
            flex: 1;
            max-width: 500px;
            margin: 0 30px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid var(--light-gray);
            border-radius: 30px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .search-button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 30px;
            padding: 7px 20px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-button:hover {
            background: var(--secondary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--dark);
            transition: color 0.3s;
            position: relative;
        }

        .header-action:hover {
            color: var(--primary);
        }

        .action-icon {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .action-text {
            font-size: 0.8rem;
            font-weight: 500;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .wishlist-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--warning);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .nav-container {
            background: var(--light);
            border-top: 1px solid var(--light-gray);
        }

        .main-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            position: relative;
        }

        .nav-links a {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s;
            z-index: 100;
        }

        .nav-links li:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu a {
            padding: 12px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--secondary);
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 500px;
            overflow: hidden;
        }

        .hero-slider .slick-dots {
            bottom: 20px;
        }

        .hero-slider .slick-dots li button:before {
            font-size: 12px;
            color: var(--white);
        }

        .hero-slide {
            height: 500px;
            position: relative;
            background-size: cover;
            background-position: center;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 10%;
            transform: translateY(-50%);
            color: var(--white);
            max-width: 500px;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 25px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .hero-btn {
            background: var(--warning);
            color: var(--white);
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .hero-btn:hover {
            background: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Categories Section */
        .section {
            padding: 60px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .section-title p {
            color: var(--gray);
            font-size: 1.1rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .category-card {
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: var(--dark);
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .category-image {
            height: 180px;
            overflow: hidden;
        }

        .category-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .category-card:hover .category-image img {
            transform: scale(1.1);
        }

        .category-info {
            padding: 20px;
            text-align: center;
        }

        .category-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .category-count {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Featured Products */
        .products-slider {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .products-slider .slick-slide {
            padding: 0 15px;
        }

        .products-slider .slick-prev, 
        .products-slider .slick-next {
            width: 40px;
            height: 40px;
            z-index: 1;
        }

        .products-slider .slick-prev {
            left: -50px;
        }

        .products-slider .slick-next {
            right: -50px;
        }

        .products-slider .slick-prev:before, 
        .products-slider .slick-next:before {
            font-size: 40px;
            color: var(--primary);
        }

        .product-card {
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--danger);
            color: var(--white);
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1;
        }

        .product-badge.sale {
            background: var(--warning);
        }

        .product-badge.new {
            background: var(--success);
        }

        .product-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.3s;
        }

        .product-card:hover .product-actions {
            opacity: 1;
            transform: translateX(0);
        }

        .product-action {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .product-action:hover {
            background: var(--primary);
            color: var(--white);
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            color: var(--gray);
            font-size: 0.8rem;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-rating {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .rating-stars {
            color: #ffc107;
            margin-right: 5px;
        }

        .rating-count {
            color: var(--gray);
            font-size: 0.8rem;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .current-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .original-price {
            font-size: 1rem;
            color: var(--gray);
            text-decoration: line-through;
        }

        .discount {
            color: var(--danger);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .add-to-cart {
            width: 100%;
            padding: 10px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-to-cart:hover {
            background: var(--secondary);
        }

        /* Special Offers */
        .offers-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            padding: 80px 0;
        }

        .offers-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .offer-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s;
        }

        .offer-card:hover {
            transform: translateY(-10px);
        }

        .offer-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .offer-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .offer-description {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Testimonials */
        .testimonials-section {
            background: var(--light);
        }

        .testimonials-slider {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .testimonial-card {
            background: var(--white);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 0 15px;
        }

        .testimonial-text {
            font-size: 1.1rem;
            font-style: italic;
            margin-bottom: 20px;
            color: var(--gray);
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }

        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-info h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .author-info p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Newsletter */
        .newsletter-section {
            background: var(--dark);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .newsletter-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .newsletter-title {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .newsletter-description {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.8;
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 30px 0 0 30px;
            font-size: 1rem;
        }

        .newsletter-input:focus {
            outline: none;
        }

        .newsletter-button {
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 0 30px 30px 0;
            padding: 0 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .newsletter-button:hover {
            background: var(--secondary);
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: var(--white);
            padding: 60px 0 0;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: var(--primary);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #b0b0b0;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .footer-about p {
            color: #b0b0b0;
            margin-bottom: 20px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .contact-info {
            list-style: none;
        }

        .contact-info li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            color: #b0b0b0;
        }

        .contact-info i {
            margin-right: 10px;
            color: var(--primary);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 60px;
            padding: 20px 0;
            text-align: center;
            color: #b0b0b0;
        }

        .footer-bottom-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .payment-methods {
            display: flex;
            gap: 10px;
        }

        .payment-method {
            width: 50px;
            height: 30px;
            background: var(--white);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .header-container {
                flex-wrap: wrap;
            }
            
            .search-bar {
                order: 3;
                max-width: 100%;
                margin: 15px 0 0;
            }
            
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .products-slider .slick-prev {
                left: -20px;
            }
            
            .products-slider .slick-next {
                right: -20px;
            }
        }

        @media (max-width: 768px) {
            .main-nav {
                flex-direction: column;
                padding: 15px 20px;
            }
            
            .nav-links {
                margin-bottom: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero-content {
                left: 5%;
                right: 5%;
                text-align: center;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-input {
                border-radius: 30px;
                margin-bottom: 10px;
            }
            
            .newsletter-button {
                border-radius: 30px;
                padding: 15px;
            }
            
            .footer-bottom-container {
                flex-direction: column;
                gap: 15px;
            }
        }

        @media (max-width: 576px) {
            .top-bar-container {
                flex-direction: column;
                gap: 5px;
            }
            
            .header-actions {
                gap: 15px;
            }
            
            .action-text {
                display: none;
            }
            
            .hero {
                height: 400px;
            }
            
            .hero-slide {
                height: 400px;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-container">
                <div class="top-bar-text">
                    <i class="fas fa-shipping-fast"></i> Free shipping on orders over $50
                </div>
                <div class="top-bar-links">
                    <a href="#"><i class="fas fa-phone"></i> Contact Us</a>
                    <a href="#"><i class="fas fa-question-circle"></i> Help Center</a>
                    <a href="#"><i class="fas fa-map-marker-alt"></i> Store Locator</a>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="main-header">
            <div class="header-container">
                <!-- Logo -->
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="logo-text">ShopEase</div>
                </a>

                <!-- Search Bar -->
                <div class="search-bar">
                    <input type="text" placeholder="Search for products, brands and more...">
                    <button class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- Header Actions -->
                <div class="header-actions">
                    <a href="wishlist.html" class="header-action">
                        <div class="action-icon">
                            <i class="far fa-heart"></i>
                        </div>
                        <div class="action-text">Wishlist</div>
                        <div class="wishlist-count">5</div>
                    </a>
                    
                    <a href="cart.html" class="header-action">
                        <div class="action-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="action-text">Cart</div>
                        <div class="cart-count">3</div>
                    </a>
                    
                    <a href="profile.html" class="header-action">
                        <div class="action-icon">
                            <i class="far fa-user"></i>
                        </div>
                        <div class="action-text">Account</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="nav-container">
            <div class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li>
                        <a href="products.html">Categories <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="category.html?cat=electronics">Electronics</a>
                            <a href="category.html?cat=clothing">Clothing</a>
                            <a href="category.html?cat=home">Home & Garden</a>
                            <a href="category.html?cat=sports">Sports & Outdoors</a>
                            <a href="category.html?cat=beauty">Beauty & Personal Care</a>
                        </div>
                    </li>
                    <li><a href="deals.html">Today's Deals</a></li>
                    <li><a href="vendors.html">Our Vendors</a></li>
                    <li><a href="about.html">About Us</a></li>
                </ul>

                <div class="auth-buttons">
                    <a href="login.html" class="btn btn-outline">Login</a>
                    <a href="register.html" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-slider">
            <div class="hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1607082350899-7e105aa886ae?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
                <div class="hero-content">
                    <h1>Summer Sale Up to 50% Off</h1>
                    <p>Discover amazing deals on your favorite products. Limited time offer!</p>
                    <a href="deals.html" class="hero-btn">Shop Now</a>
                </div>
            </div>
            <div class="hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
                <div class="hero-content">
                    <h1>New Tech Gadgets Arrived</h1>
                    <p>Check out the latest smartphones, laptops, and smart devices.</p>
                    <a href="category.html?cat=electronics" class="hero-btn">Explore Tech</a>
                </div>
            </div>
            <div class="hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
                <div class="hero-content">
                    <h1>Fashion for Every Season</h1>
                    <p>Update your wardrobe with our latest clothing collection.</p>
                    <a href="category.html?cat=clothing" class="hero-btn">Discover Fashion</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section">
        <div class="section-title">
            <h2>Shop by Category</h2>
            <p>Explore our wide range of product categories</p>
        </div>

        <div class="categories-grid">
            <a href="category.html?cat=electronics" class="category-card">
                <div class="category-image">
                    <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Electronics">
                </div>
                <div class="category-info">
                    <div class="category-name">Electronics</div>
                    <div class="category-count">245 Products</div>
                </div>
            </a>

            <a href="category.html?cat=clothing" class="category-card">
                <div class="category-image">
                    <img src="https://images.unsplash.com/photo-1445205170230-053b83016050?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Clothing">
                </div>
                <div class="category-info">
                    <div class="category-name">Clothing</div>
                    <div class="category-count">189 Products</div>
                </div>
            </a>

            <a href="category.html?cat=home" class="category-card">
                <div class="category-image">
                    <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Home & Garden">
                </div>
                <div class="category-info">
                    <div class="category-name">Home & Garden</div>
                    <div class="category-count">156 Products</div>
                </div>
            </a>

            <a href="category.html?cat=sports" class="category-card">
                <div class="category-image">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Sports & Outdoors">
                </div>
                <div class="category-info">
                    <div class="category-name">Sports & Outdoors</div>
                    <div class="category-count">98 Products</div>
                </div>
            </a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="section" style="background: var(--light);">
        <div class="section-title">
            <h2>Featured Products</h2>
            <p>Check out our most popular items</p>
        </div>

        <div class="products-slider">
            <!-- Product 1 -->
            <div class="product-card">
                <div class="product-badge sale">Sale</div>
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Wireless Headphones">
                    <div class="product-actions">
                        <a href="#" class="product-action" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </a>
                        <a href="#" class="product-action" title="Quick View">
                            <i class="far fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="product-info">
                    <div class="product-category">Electronics</div>
                    <div class="product-name">Premium Wireless Headphones with Noise Cancellation</div>
                    <div class="product-rating">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="rating-count">(128)</div>
                    </div>
                    <div class="product-price">
                        <div class="current-price">$129.99</div>
                        <div class="original-price">$179.99</div>
                        <div class="discount">28% off</div>
                    </div>
                    <button class="add-to-cart">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="product-card">
                <div class="product-badge new">New</div>
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Smart Watch">
                    <div class="product-actions">
                        <a href="#" class="product-action" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </a>
                        <a href="#" class="product-action" title="Quick View">
                            <i class="far fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="product-info">
                    <div class="product-category">Electronics</div>
                    <div class="product-name">Smart Watch with Health Monitoring</div>
                    <div class="product-rating">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <div class="rating-count">(64)</div>
                    </div>
                    <div class="product-price">
                        <div class="current-price">$199.99</div>
                    </div>
                    <button class="add-to-cart">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1588099768531-a72d4a198538?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Backpack">
                    <div class="product-actions">
                        <a href="#" class="product-action" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </a>
                        <a href="#" class="product-action" title="Quick View">
                            <i class="far fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="product-info">
                    <div class="product-category">Fashion</div>
                    <div class="product-name">Waterproof Hiking Backpack 40L</div>
                    <div class="product-rating">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="rating-count">(42)</div>
                    </div>
                    <div class="product-price">
                        <div class="current-price">$79.99</div>
                    </div>
                    <button class="add-to-cart">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="product-card">
                <div class="product-badge sale">Sale</div>
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Smart Speaker">
                    <div class="product-actions">
                        <a href="#" class="product-action" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </a>
                        <a href="#" class="product-action" title="Quick View">
                            <i class="far fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="product-info">
                    <div class="product-category">Electronics</div>
                    <div class="product-name">Smart Speaker with Voice Assistant</div>
                    <div class="product-rating">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="rating-count">(89)</div>
                    </div>
                    <div class="product-price">
                        <div class="current-price">$89.99</div>
                        <div class="original-price">$119.99</div>
                        <div class="discount">25% off</div>
                    </div>
                    <button class="add-to-cart">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Special Offers -->
    <section class="offers-section">
        <div class="section-title" style="color: var(--white);">
            <h2>Why Shop With Us?</h2>
            <p>We provide the best shopping experience</p>
        </div>

        <div class="offers-container">
            <div class="offer-card">
                <div class="offer-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="offer-title">Free Shipping</div>
                <div class="offer-description">Free delivery on orders over $50. Fast and reliable shipping to your doorstep.</div>
            </div>

            <div class="offer-card">
                <div class="offer-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="offer-title">Secure Payment</div>
                <div class="offer-description">Your payment information is safe with us. We use industry-standard encryption.</div>
            </div>

            <div class="offer-card">
                <div class="offer-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <div class="offer-title">Easy Returns</div>
                <div class="offer-description">Not satisfied? Return within 30 days for a full refund. No questions asked.</div>
            </div>

            <div class="offer-card">
                <div class="offer-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="offer-title">24/7 Support</div>
                <div class="offer-description">Our customer service team is available around the clock to help you.</div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section testimonials-section">
        <div class="section-title">
            <h2>What Our Customers Say</h2>
            <p>Read genuine reviews from our happy customers</p>
        </div>

        <div class="testimonials-slider">
            <div class="testimonial-card">
                <div class="testimonial-text">
                    "I've been shopping here for over a year now and I'm always impressed with the quality of products and the fast shipping. The customer service is exceptional!"
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah Johnson">
                    </div>
                    <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <p>Verified Customer</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-text">
                    "The return process was so easy when I received a damaged item. The support team was helpful and I got my replacement within 3 days. Highly recommended!"
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Michael Chen">
                    </div>
                    <div class="author-info">
                        <h4>Michael Chen</h4>
                        <p>Verified Customer</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-text">
                    "Great prices and an amazing selection of products. I found exactly what I was looking for and the delivery was faster than expected. Will shop here again!"
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Emily Rodriguez">
                    </div>
                    <div class="author-info">
                        <h4>Emily Rodriguez</h4>
                        <p>Verified Customer</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="newsletter-container">
            <h2 class="newsletter-title">Subscribe to Our Newsletter</h2>
            <p class="newsletter-description">Get the latest updates on new products and upcoming sales</p>
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Your email address" required>
                <button type="submit" class="newsletter-button">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3>ShopEase</h3>
                <div class="footer-about">
                    <p>Your one-stop destination for all your shopping needs. We offer quality products at affordable prices with excellent customer service.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                    <li><a href="faq.html">FAQ</a></li>
                    <li><a href="blog.html">Blog</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Customer Service</h3>
                <ul class="footer-links">
                    <li><a href="shipping.html">Shipping Information</a></li>
                    <li><a href="returns.html">Returns & Refunds</a></li>
                    <li><a href="size-guide.html">Size Guide</a></li>
                    <li><a href="privacy.html">Privacy Policy</a></li>
                    <li><a href="terms.html">Terms & Conditions</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Contact Info</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Commerce Street, Suite 100<br>New York, NY 10001</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>+1 (555) 123-4567</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>support@shopease.com</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-container">
                <div class="copyright">
                    &copy; 2023 ShopEase. All rights reserved.
                </div>
                <div class="payment-methods">
                    <div class="payment-method">Visa</div>
                    <div class="payment-method">MC</div>
                    <div class="payment-method">PayPal</div>
                    <div class="payment-method">Amex</div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            // Hero Slider
            $('.hero-slider').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 5000
            });

            // Products Slider
            $('.products-slider').slick({
                dots: false,
                arrows: true,
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: 1,
                responsive: [
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });

            // Testimonials Slider
            $('.testimonials-slider').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 6000
            });

            // Add to Cart Functionality
            $('.add-to-cart').click(function(e) {
                e.preventDefault();
                const productCard = $(this).closest('.product-card');
                const productName = productCard.find('.product-name').text();
                
                // Update cart count
                const currentCount = parseInt($('.cart-count').text());
                $('.cart-count').text(currentCount + 1);
                
                // Show confirmation
                alert(`${productName} added to cart!`);
            });

            // Wishlist Functionality
            $('.product-action .fa-heart').click(function(e) {
                e.preventDefault();
                const heartIcon = $(this);
                
                if (heartIcon.hasClass('far')) {
                    heartIcon.removeClass('far').addClass('fas');
                    
                    // Update wishlist count
                    const currentCount = parseInt($('.wishlist-count').text());
                    $('.wishlist-count').text(currentCount + 1);
                    
                    alert('Added to wishlist!');
                } else {
                    heartIcon.removeClass('fas').addClass('far');
                    
                    // Update wishlist count
                    const currentCount = parseInt($('.wishlist-count').text());
                    $('.wishlist-count').text(Math.max(0, currentCount - 1));
                    
                    alert('Removed from wishlist!');
                }
            });

            // Newsletter Form Submission
            $('.newsletter-form').submit(function(e) {
                e.preventDefault();
                const email = $(this).find('input[type="email"]').val();
                
                if (email) {
                    alert(`Thank you for subscribing with ${email}!`);
                    $(this).find('input[type="email"]').val('');
                }
            });
        });
    </script>
</body>
</html>