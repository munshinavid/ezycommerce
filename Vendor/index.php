<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EzyCommerce - Your One-Stop Shop</title>
    <link rel="stylesheet" href="ind.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <h1><i class="fas fa-shopping-bag"></i> EzyCommerce</h1>
            </div>
            
            <div class="search-bar">
                <input type="text" placeholder="Search products..." id="searchInput">
                <button type="button" id="searchBtn"><i class="fas fa-search"></i></button>
            </div>
            
            <div class="nav-actions">
                <div class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </div>
                <div class="user-menu">
                    <button class="login-btn">Login</button>
                    <button class="register-btn">Register</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="main-nav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="#" class="active">Home</a></li>
                <li class="dropdown">
                    <a href="#">Categories <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-content">
                        <a href="#" data-category="electronics">Electronics</a>
                        <a href="#" data-category="clothing">Clothing</a>
                        <a href="#" data-category="books">Books</a>
                        <a href="#" data-category="home">Home & Garden</a>
                        <a href="#" data-category="sports">Sports</a>
                    </div>
                </li>
                <li><a href="#">Best Sellers</a></li>
                <li><a href="#">New Arrivals</a></li>
                <li><a href="#">Deals</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <div class="hero-content">
                <h2>Welcome to EzyCommerce</h2>
                <p>Discover amazing products at unbeatable prices</p>
                <button class="cta-btn">Shop Now</button>
            </div>
        </div>
    </section>

    <!-- Filters and Sorting -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-wrapper">
                <div class="filter-group">
                    <label for="categoryFilter">Category:</label>
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="electronics">Electronics</option>
                        <option value="clothing">Clothing</option>
                        <option value="books">Books</option>
                        <option value="home">Home & Garden</option>
                        <option value="sports">Sports</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="priceFilter">Price Range:</label>
                    <select id="priceFilter">
                        <option value="">All Prices</option>
                        <option value="0-50">$0 - $50</option>
                        <option value="50-100">$50 - $100</option>
                        <option value="100-200">$100 - $200</option>
                        <option value="200+">$200+</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sortBy">Sort By:</label>
                    <select id="sortBy">
                        <option value="name">Name</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="newest">Newest</option>
                        <option value="rating">Rating</option>
                    </select>
                </div>
                
                <button class="clear-filters" id="clearFilters">Clear Filters</button>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <main class="products-section">
        <div class="container">
            <div class="section-header">
                <h2>Featured Products</h2>
                <p>Discover our handpicked selection</p>
            </div>
            
            <div class="products-grid" id="productsGrid">
                <!-- Product cards will be dynamically loaded here -->
                <!-- Demo products for layout -->
                <div class="product-card" data-product-id="1" data-category="electronics" data-price="299.99">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250x200/007bff/ffffff?text=Smartphone" alt="Smartphone">
                        <div class="product-badges">
                            <span class="badge sale">20% OFF</span>
                        </div>
                        <div class="product-overlay">
                            <button class="quick-view-btn" data-product-id="1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Premium Smartphone</h3>
                        <p class="product-description">Latest model with advanced features</p>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="rating-count">(124)</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">$299.99</span>
                            <span class="original-price">$374.99</span>
                        </div>
                        <div class="product-stock">
                            <span class="stock-status in-stock">In Stock (15 left)</span>
                        </div>
                        <button class="add-to-cart-btn" data-product-id="1">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="product-card" data-product-id="2" data-category="clothing" data-price="49.99">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250x200/28a745/ffffff?text=T-Shirt" alt="T-Shirt">
                        <div class="product-badges">
                            <span class="badge new">NEW</span>
                        </div>
                        <div class="product-overlay">
                            <button class="quick-view-btn" data-product-id="2">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Premium Cotton T-Shirt</h3>
                        <p class="product-description">Comfortable and stylish everyday wear</p>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-count">(89)</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">$49.99</span>
                        </div>
                        <div class="product-stock">
                            <span class="stock-status in-stock">In Stock (32 left)</span>
                        </div>
                        <button class="add-to-cart-btn" data-product-id="2">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="product-card" data-product-id="3" data-category="books" data-price="24.99">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250x200/dc3545/ffffff?text=Book" alt="Book">
                        <div class="product-overlay">
                            <button class="quick-view-btn" data-product-id="3">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Web Development Guide</h3>
                        <p class="product-description">Complete guide to modern web development</p>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="rating-count">(67)</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">$24.99</span>
                        </div>
                        <div class="product-stock">
                            <span class="stock-status in-stock">In Stock (8 left)</span>
                        </div>
                        <button class="add-to-cart-btn" data-product-id="3">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="product-card" data-product-id="4" data-category="home" data-price="129.99">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250x200/ffc107/000000?text=Home+Decor" alt="Home Decor">
                        <div class="product-badges">
                            <span class="badge bestseller">BESTSELLER</span>
                        </div>
                        <div class="product-overlay">
                            <button class="quick-view-btn" data-product-id="4">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Modern Table Lamp</h3>
                        <p class="product-description">Elegant lighting for your home</p>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-count">(156)</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">$129.99</span>
                        </div>
                        <div class="product-stock">
                            <span class="stock-status low-stock">Low Stock (3 left)</span>
                        </div>
                        <button class="add-to-cart-btn" data-product-id="4">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="product-card" data-product-id="5" data-category="sports" data-price="79.99">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250x200/6f42c1/ffffff?text=Sports+Gear" alt="Sports Gear">
                        <div class="product-overlay">
                            <button class="quick-view-btn" data-product-id="5">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Fitness Equipment Set</h3>
                        <p class="product-description">Complete home workout solution</p>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="rating-count">(92)</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">$79.99</span>
                        </div>
                        <div class="product-stock">
                            <span class="stock-status in-stock">In Stock (21 left)</span>
                        </div>
                        <button class="add-to-cart-btn" data-product-id="5">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="product-card" data-product-id="6" data-category="electronics" data-price="189.99">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/250x200/20c997/ffffff?text=Headphones" alt="Headphones">
                        <div class="product-badges">
                            <span class="badge sale">15% OFF</span>
                        </div>
                        <div class="product-overlay">
                            <button class="quick-view-btn" data-product-id="6">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Wireless Headphones</h3>
                        <p class="product-description">Premium sound quality and comfort</p>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-count">(203)</span>
                        </div>
                        <div class="product-price">
                            <span class="current-price">$189.99</span>
                            <span class="original-price">$223.99</span>
                        </div>
                        <div class="product-stock">
                            <span class="stock-status out-of-stock">Out of Stock</span>
                        </div>
                        <button class="add-to-cart-btn" data-product-id="6" disabled>
                            <i class="fas fa-ban"></i> Out of Stock
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="page-btn prev" disabled>
                    <i class="fas fa-chevron-left"></i> Previous
                </button>
                <div class="page-numbers">
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <span class="page-dots">...</span>
                    <button class="page-btn">10</button>
                </div>
                <button class="page-btn next">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>EzyCommerce</h3>
                    <p>Your trusted online shopping destination with quality products and excellent service.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Customer Service</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Track Your Order</a></li>
                        <li><a href="#">Returns & Refunds</a></li>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Size Guide</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                        <p><i class="fas fa-envelope"></i> support@ezycommerce.com</p>
                        <p><i class="fas fa-map-marker-alt"></i> 123 Commerce St, City, State 12345</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 EzyCommerce. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Quick View Modal (will be handled by JS later) -->
    <div id="quickViewModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="quickViewContent">
                <!-- Quick view content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>