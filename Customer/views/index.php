<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasy - Modern Online Shopping</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <style>
        :root {
            --primary: #3a86ff;
            --primary-dark: #2563eb;
            --secondary: #ff006e;
            --accent: #8338ec;
            --success: #4cc9f0;
            --warning: #ffbe0b;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Header Styles */
        .top-bar {
            background-color: var(--dark);
            color: white;
            padding: 8px 0;
            font-size: 14px;
        }

        .top-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .top-bar-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            transition: var(--transition);
        }

        .top-bar-links a:hover {
            color: var(--primary);
        }

        header {
            background-color: white;
            box-shadow: var(--box-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
        }

        .search-container {
            display: flex;
            align-items: center;
            width: 50%;
        }

        .category-dropdown {
            position: relative;
        }

        .category-toggle {
            padding: 12px 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm) 0 0 var(--border-radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .category-toggle i {
            margin-left: 8px;
            font-size: 12px;
        }

        .category-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 220px;
            background-color: white;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--box-shadow);
            padding: 10px 0;
            z-index: 10;
            display: none;
        }

        .category-dropdown:hover .category-menu {
            display: block;
        }

        .category-menu a {
            display: block;
            padding: 10px 20px;
            color: var(--dark);
            text-decoration: none;
            transition: var(--transition);
        }

        .category-menu a:hover {
            background-color: var(--light);
            color: var(--primary);
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: var(--light);
            border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
            padding: 0 15px;
            flex: 1;
        }

        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            padding: 12px 10px;
            width: 100%;
            font-size: 16px;
        }

        .search-btn {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
        }

        .header-icons {
            display: flex;
            gap: 20px;
        }

        .icon-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: var(--dark);
            transition: var(--transition);
        }

        .icon-btn:hover {
            color: var(--primary);
        }

        .icon-btn .count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--secondary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Navigation */
        nav {
            background-color: white;
            border-top: 1px solid var(--light-gray);
            border-bottom: 1px solid var(--light-gray);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-menu li a {
            display: block;
            padding: 15px 0;
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-menu li a:hover {
            color: var(--primary);
        }

        .nav-menu li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background-color: var(--primary);
            transition: var(--transition);
        }

        .nav-menu li a:hover::after {
            width: 100%;
        }

        /* Hero Banner */
        .hero-banner {
            position: relative;
            height: 500px;
            overflow: hidden;
        }

        .banner-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
        }

        .banner-slide.active {
            opacity: 1;
        }

        .banner-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .banner-content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            max-width: 600px;
        }

        .banner-content p {
            font-size: 18px;
            margin-bottom: 30px;
            max-width: 500px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: var(--border-radius-sm);
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            margin-left: 15px;
        }

        .btn-outline:hover {
            background-color: white;
            color: var(--primary);
        }

        /* Filter Row */
        .filter-row {
            background-color: white;
            padding: 20px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .filter-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-tabs {
            display: flex;
            gap: 15px;
        }

        .filter-tab {
            padding: 10px 20px;
            background-color: white;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .filter-tab.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .filter-dropdowns {
            display: flex;
            gap: 15px;
        }

        .filter-dropdown {
            position: relative;
        }

        .filter-dropdown-toggle {
            padding: 10px 15px;
            background-color: white;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .filter-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--box-shadow);
            padding: 10px;
            z-index: 10;
            min-width: 200px;
            display: none;
        }

        .filter-dropdown:hover .filter-dropdown-menu {
            display: block;
        }

        .filter-option {
            padding: 8px 10px;
            cursor: pointer;
            transition: var(--transition);
            border-radius: var(--border-radius-sm);
        }

        .filter-option:hover {
            background-color: var(--light);
        }

        /* Products Section */
        .section {
            padding: 40px 0;
        }

        .products-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .product-card {
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }

        .badge-new {
            background-color: var(--success);
            color: white;
        }

        .badge-sale {
            background-color: var(--secondary);
            color: white;
        }

        .badge-hot {
            background-color: var(--warning);
            color: var(--dark);
        }

        .product-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 5px;
        }

        .product-title {
            font-size: 16px;
            margin-bottom: 10px;
            color: var(--dark);
            font-weight: 600;
        }

        .product-rating {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .stars {
            color: #ffc107;
            margin-right: 5px;
        }

        .rating-count {
            color: var(--gray);
            font-size: 14px;
            margin-left: 5px;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .current-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .original-price {
            text-decoration: line-through;
            color: var(--gray);
        }

        .discount {
            background-color: var(--secondary);
            color: white;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .btn-cart {
            flex: 1;
            padding: 10px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-cart:hover {
            background-color: var(--primary-dark);
        }

        .btn-wishlist {
            width: 40px;
            height: 40px;
            border-radius: var(--border-radius-sm);
            background-color: var(--light);
            color: var(--gray);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-wishlist:hover {
            background-color: var(--secondary);
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            gap: 8px;
        }

        .pagination button {
            padding: 10px 16px;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--light-gray);
            background-color: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .pagination button.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination button:hover:not(.active) {
            background-color: var(--light);
        }

        /* Newsletter Section */
        .newsletter {
            background: linear-gradient(rgba(58, 134, 255, 0.9), rgba(37, 99, 235, 0.9)), url('https://images.unsplash.com/photo-1581349434648-8e4323df9e143?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 80px 20px;
        }

        .newsletter h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .newsletter p {
            max-width: 600px;
            margin: 0 auto 30px;
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-form input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: var(--border-radius-sm) 0 0 var(--border-radius-sm);
            outline: none;
            font-size: 16px;
        }

        .newsletter-form button {
            padding: 15px 25px;
            background-color: var(--warning);
            color: var(--dark);
            border: none;
            border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .newsletter-form button:hover {
            background-color: #ffaa00;
        }

        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #cbd5e0;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-column ul li a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4a5568;
            color: white;
            transition: var(--transition);
        }

        .social-icons a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 40px;
            border-top: 1px solid #4a5568;
            max-width: 1200px;
            margin: 40px auto 0;
            padding: 30px 20px 0;
        }

        /* Notification Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .toast {
            background-color: white;
            border-radius: var(--border-radius-sm);
            padding: 15px 20px;
            margin-bottom: 10px;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease, fadeOut 0.5s ease 2.5s forwards;
            min-width: 300px;
        }

        .toast-success {
            border-left: 4px solid var(--success);
        }

        .toast-error {
            border-left: 4px solid var(--secondary);
        }

        .toast-warning {
            border-left: 4px solid var(--warning);
        }

        .toast-icon {
            margin-right: 15px;
            font-size: 20px;
        }

        .toast-success .toast-icon {
            color: var(--success);
        }

        .toast-error .toast-icon {
            color: var(--secondary);
        }

        .toast-warning .toast-icon {
            color: var(--warning);
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .toast-message {
            font-size: 14px;
            color: var(--gray);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .search-container {
                width: 40%;
            }

            .banner-content h1 {
                font-size: 36px;
            }

            .filter-container {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .filter-dropdowns {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .top-bar-content {
                flex-direction: column;
                gap: 10px;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .search-container {
                width: 100%;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .banner-content h1 {
                font-size: 28px;
            }

            .filter-tabs {
                flex-wrap: wrap;
            }

            .newsletter-form {
                flex-direction: column;
                gap: 10px;
            }

            .newsletter-form input,
            .newsletter-form button {
                border-radius: var(--border-radius-sm);
            }
        }

        @media (max-width: 576px) {
            .product-actions {
                flex-direction: column;
            }

            .btn-cart, .btn-wishlist {
                width: 100%;
            }

            .pagination {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="top-bar-text">Free shipping on orders over $50</div>
            <div class="top-bar-links">
                <a href="#">Help</a>
                <a href="#">Contact</a>
                <a href="#">Order Tracking</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header>
        <div class="header-content">
            <a href="#" class="logo">
                <i class="fas fa-shopping-bag"></i>
                ShopEasy
            </a>
            
            <div class="search-container">
                <div class="category-dropdown">
                    <button class="category-toggle">
                        All Categories <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="category-menu">
                        <a href="#">Electronics</a>
                        <a href="#">Clothing</a>
                        <a href="#">Home & Kitchen</a>
                        <a href="#">Books</a>
                        <a href="#">Sports</a>
                        <a href="#">Beauty</a>
                    </div>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Search for products...">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="header-icons">
                <button class="icon-btn">
                    <i class="fas fa-user"></i>
                </button>
                <button class="icon-btn">
                    <i class="fas fa-heart"></i>
                    <span class="count" id="wishlist-count">3</span>
                </button>
                <button class="icon-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="count" id="cart-count">5</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <ul class="nav-menu">
                <li><a href="#" class="active">Home</a></li>
                <li><a href="#">New Arrivals</a></li>
                <li><a href="#">Best Sellers</a></li>
                <li><a href="#">Sale</a></li>
                <li><a href="#">Electronics</a></li>
                <li><a href="#">Clothing</a></li>
                <li><a href="#">Home & Kitchen</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="banner-slide active" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1607082350899-7e105aa886ae?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
            <div class="banner-content">
                <h1>Summer Sale Up To 50% Off</h1>
                <p>Discover the latest trends and get the best deals on thousands of products. Free shipping on orders over $50.</p>
                <div>
                    <button class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </button>
                    <button class="btn btn-outline">
                        <i class="fas fa-eye"></i> View Collection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Row -->
    <div class="filter-row">
        <div class="filter-container">
            <div class="filter-tabs">
                <div class="filter-tab active" data-filter="all">All Products</div>
                <div class="filter-tab" data-filter="new">New Arrivals</div>
                <div class="filter-tab" data-filter="bestseller">Best Sellers</div>
                <div class="filter-tab" data-filter="sale">On Sale</div>
            </div>
            <div class="filter-dropdowns">
                <div class="filter-dropdown">
                    <div class="filter-dropdown-toggle">
                        <i class="fas fa-filter"></i>
                        Filter
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="filter-dropdown-menu">
                        <div class="filter-option" data-filter="price-low">Price: Low to High</div>
                        <div class="filter-option" data-filter="price-high">Price: High to Low</div>
                        <div class="filter-option" data-filter="stock">In Stock</div>
                        <div class="filter-option" data-filter="rating">Highest Rated</div>
                    </div>
                </div>
                <div class="filter-dropdown">
                    <div class="filter-dropdown-toggle">
                        <i class="fas fa-sort-amount-down"></i>
                        Sort By
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="filter-dropdown-menu">
                        <div class="filter-option" data-sort="newest">Newest First</div>
                        <div class="filter-option" data-sort="popular">Most Popular</div>
                        <div class="filter-option" data-sort="name">Name A-Z</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <section class="section">
        <div class="products-container">
            <div class="products-grid" id="products-container">
                <!-- Products will be loaded dynamically -->
            </div>
            
            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">Next <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <h2>Subscribe to Our Newsletter</h2>
        <p>Get the latest updates on new products, special offers, and sales</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email address">
            <button type="submit">Subscribe</button>
        </form>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>ShopEasy</h3>
                <p>Your one-stop destination for all your shopping needs. Quality products at affordable prices.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Shop</h3>
                <ul>
                    <li><a href="#">Electronics</a></li>
                    <li><a href="#">Clothing</a></li>
                    <li><a href="#">Home & Kitchen</a></li>
                    <li><a href="#">Books</a></li>
                    <li><a href="#">Sports</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Shipping & Returns</a></li>
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact Info</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Commerce St, City, Country</li>
                    <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                    <li><i class="fas fa-envelope"></i> support@shopeasy.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 ShopEasy. All rights reserved.</p>
        </div>
    </footer>

    <!-- Notification Toast Container -->
    <div class="toast-container" id="toast-container">
        <!-- Toasts will be added here dynamically -->
    </div>

    <!-- JavaScript Files -->
    <script src="../scripts/home.js"></script>
</body>
</html>