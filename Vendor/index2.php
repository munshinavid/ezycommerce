<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasy - Online Shopping</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
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
        header {
            background-color: white;
            box-shadow: var(--box-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            border-bottom: 1px solid #eee;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: #f5f7fb;
            border-radius: 30px;
            padding: 8px 15px;
            width: 50%;
        }

        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            padding: 5px 10px;
            width: 100%;
            font-size: 16px;
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
            font-size: 18px;
            color: #555;
        }

        .icon-btn .count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav {
            padding: 15px 5%;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-menu li a {
            text-decoration: none;
            color: #444;
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-menu li a:hover {
            color: var(--primary);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(67, 97, 238, 0.9), rgba(63, 55, 201, 0.9)), url('https://images.unsplash.com/photo-1607082350899-7e105aa886ae?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 5%;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: white;
            color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        /* Categories Section */
        .section {
            padding: 60px 5%;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .category-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .category-card h3 {
            margin-bottom: 10px;
        }

        /* Products Section */
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .product-card {
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
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
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .product-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .current-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        .original-price {
            text-decoration: line-through;
            color: #6c757d;
        }

        .discount {
            background-color: var(--danger);
            color: white;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
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
            color: #6c757d;
            font-size: 14px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .btn-cart {
            flex: 1;
            background-color: var(--primary);
            color: white;
        }

        .btn-wishlist {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .btn-wishlist:hover {
            background-color: var(--danger);
            color: white;
        }

        /* Featured Section */
        .featured {
            background-color: white;
        }

        /* Newsletter Section */
        .newsletter {
            background: linear-gradient(rgba(67, 97, 238, 0.9), rgba(63, 55, 201, 0.9)), url('https://images.unsplash.com/photo-1581349434648-8e4323df9e143?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 80px 5%;
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
            padding: 15px;
            border: none;
            border-radius: 30px 0 0 30px;
            outline: none;
        }

        .newsletter-form button {
            padding: 15px 25px;
            background-color: var(--warning);
            color: white;
            border: none;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
            font-weight: 500;
        }

        /* Footer */
        footer {
            background-color: #2d3748;
            color: white;
            padding: 60px 5% 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
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
            border-top: 1px solid #4a5568;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .search-bar {
                width: 40%;
            }
        }

        @media (max-width: 768px) {
            .top-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .search-bar {
                width: 100%;
            }
            
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
            
            .hero h1 {
                font-size: 36px;
            }
            
            .newsletter-form {
                flex-direction: column;
                gap: 10px;
            }
            
            .newsletter-form input,
            .newsletter-form button {
                border-radius: 30px;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 28px;
            }
            
            .section-title h2 {
                font-size: 24px;
            }
            
            .product-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="top-header">
            <a href="index.php" class="logo">
                <i class="fas fa-shopping-bag"></i>
                ShopEasy
            </a>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for products...">
            </div>
            <div class="header-icons">
                <button class="icon-btn">
                    <i class="fas fa-user"></i>
                </button>
                <button class="icon-btn">
                    <i class="fas fa-heart"></i>
                    <span class="count">3</span>
                </button>
                <button class="icon-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="count">5</span>
                </button>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="#" aria-disabled="true" tabindex="-1">Electronics</a></li>
                <li><a href="#" aria-disabled="true" tabindex="-1">Clothing</a></li>
                <li><a href="#" aria-disabled="true" tabindex="-1">Home & Kitchen</a></li>
                <li><a href="#" aria-disabled="true" tabindex="-1">Books</a></li>
                <li><a href="#" aria-disabled="true" tabindex="-1">Sports</a></li>
                <li><a href="#" aria-disabled="true" tabindex="-1">Beauty</a></li>
                <li><a href="#" aria-disabled="true" tabindex="-1">Sale</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Summer Sale Up To 50% Off</h1>
            <p>Discover the latest trends and get the best deals on thousands of products. Free shipping on orders over $50.</p>
            <button class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Shop Now
            </button>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section">
        <div class="section-title">
            <h2>Shop By Category</h2>
            <p>Browse products by your favorite categories</p>
        </div>
        <div class="categories">
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Electronics</h3>
                <p>128 products</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h3>Clothing</h3>
                <p>92 products</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-couch"></i>
                </div>
                <h3>Home & Kitchen</h3>
                <p>76 products</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Books</h3>
                <p>215 products</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-running"></i>
                </div>
                <h3>Sports</h3>
                <p>64 products</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <h3>Beauty</h3>
                <p>48 products</p>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="section">
        <div class="section-title">
            <h2>Featured Products</h2>
            <p>Our most popular products based on sales</p>
        </div>
        <div class="products">
            <!-- Product 1 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Wireless Headphones" class="product-image">
                <div class="product-info">
                    <div class="product-category">Electronics</div>
                    <h3 class="product-title">Wireless Headphones</h3>
                    <div class="product-price">
                        <span class="current-price">$129.99</span>
                        <span class="original-price">$159.99</span>
                        <span class="discount">19% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="rating-count">(128)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Product 2 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Running Shoes" class="product-image">
                <div class="product-info">
                    <div class="product-category">Sports</div>
                    <h3 class="product-title">Running Shoes</h3>
                    <div class="product-price">
                        <span class="current-price">$89.99</span>
                        <span class="original-price">$119.99</span>
                        <span class="discount">25% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="rating-count">(86)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Product 3 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Smart Watch" class="product-image">
                <div class="product-info">
                    <div class="product-category">Electronics</div>
                    <h3 class="product-title">Smart Watch</h3>
                    <div class="product-price">
                        <span class="current-price">$199.99</span>
                        <span class="original-price">$249.99</span>
                        <span class="discount">20% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="rating-count">(215)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Product 4 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Sports T-Shirt" class="product-image">
                <div class="product-info">
                    <div class="product-category">Clothing</div>
                    <h3 class="product-title">Sports T-Shirt</h3>
                    <div class="product-price">
                        <span class="current-price">$24.99</span>
                        <span class="original-price">$39.99</span>
                        <span class="discount">38% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="rating-count">(42)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="section featured">
        <div class="section-title">
            <h2>New Arrivals</h2>
            <p>Brand new products just added to our store</p>
        </div>
        <div class="products">
            <!-- Product 5 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1504274066651-8d31a536b11a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Coffee Maker" class="product-image">
                <div class="product-info">
                    <div class="product-category">Home & Kitchen</div>
                    <h3 class="product-title">Coffee Maker</h3>
                    <div class="product-price">
                        <span class="current-price">$79.99</span>
                        <span class="original-price">$99.99</span>
                        <span class="discount">20% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="rating-count">(57)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Product 6 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1531297484001-80022131f5a1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Laptop" class="product-image">
                <div class="product-info">
                    <div class="product-category">Electronics</div>
                    <h3 class="product-title">Ultrabook Laptop</h3>
                    <div class="product-price">
                        <span class="current-price">$999.99</span>
                        <span class="original-price">$1299.99</span>
                        <span class="discount">23% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="rating-count">(132)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Product 7 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1585386959984-a4155224a1ad?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Perfume" class="product-image">
                <div class="product-info">
                    <div class="product-category">Beauty</div>
                    <h3 class="product-title">Luxury Perfume</h3>
                    <div class="product-price">
                        <span class="current-price">$59.99</span>
                        <span class="original-price">$79.99</span>
                        <span class="discount">25% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="rating-count">(78)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Product 8 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Smart Home Speaker" class="product-image">
                <div class="product-info">
                    <div class="product-category">Electronics</div>
                    <h3 class="product-title">Smart Home Speaker</h3>
                    <div class="product-price">
                        <span class="current-price">$129.99</span>
                        <span class="original-price">$159.99</span>
                        <span class="discount">19% off</span>
                    </div>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="rating-count">(91)</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
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
                        <li><a href="#" aria-disabled="true" tabindex="-1">Contact Us</a></li>
                        <li><a href="#" aria-disabled="true" tabindex="-1">FAQs</a></li>
                        <li><a href="#" aria-disabled="true" tabindex="-1">Shipping & Returns</a></li>
                        <li><a href="#" aria-disabled="true" tabindex="-1">Track Order</a></li>
                        <li><a href="#" aria-disabled="true" tabindex="-1">Privacy Policy</a></li>
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

    <!-- JavaScript File -->
    <script src="script.js"></script>
</body>
</html>