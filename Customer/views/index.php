<?php
$pageTitle = 'EzyCommerce — Discover Premium Products Online';
$pageDescription = 'Shop the latest trends in fashion, electronics, and lifestyle at EzyCommerce. Free shipping on orders over $50 with 30-day returns.';
require_once __DIR__ . "/components/header.php";
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-banner" id="discount-banners">
            <div class="hero-content">
                <div class="hero-tag">New Collection 2026</div>
                <h1 class="hero-title">Discover Your <span>Perfect Style</span> Today</h1>
                <p class="hero-subtitle">Explore our curated collection of premium products. Quality meets modern design in every piece we offer.</p>
                <div class="hero-actions">
                    <a href="#products" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </a>
                    <a href="#categories" class="btn btn-glass">
                        <i class="fas fa-compass"></i> Explore
                    </a>
                </div>
            </div>
            <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Fashion Collection" class="hero-image" style="border-radius: 20px;">
        </div>
    </section>

    <!-- Trust Badges -->
    <div class="container">
        <div class="trust-badges">
            <div class="trust-badge">
                <div class="trust-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="trust-text">
                    <h4>Secure Payment</h4>
                    <p>100% secure checkout</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-icon"><i class="fas fa-truck"></i></div>
                <div class="trust-text">
                    <h4>Free Shipping</h4>
                    <p>On orders over $50</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-icon"><i class="fas fa-undo-alt"></i></div>
                <div class="trust-text">
                    <h4>30-Day Returns</h4>
                    <p>No questions asked</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-icon"><i class="fas fa-headset"></i></div>
                <div class="trust-text">
                    <h4>24/7 Support</h4>
                    <p>Dedicated help desk</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Cards -->
    <section class="categories-section" id="categories">
        <div class="container">
            <h2 class="section-title">Shop By Category</h2>
            <div class="category-grid" id="categories-container">
                <!-- Categories loaded via AJAX -->
                <a href="#" class="category-card active" data-category="">
                    <i class="fas fa-th-large"></i> All
                </a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Products</h2>
                <div class="product-filters" id="product-filters">
                    <button class="filter-tab active" data-filter="all">All Items</button>
                    <button class="filter-tab" data-filter="new">New Arrivals</button>
                    <button class="filter-tab" data-filter="sale">On Sale</button>
                    <button class="filter-tab" data-filter="popular">Popular</button>
                </div>
            </div>
            
            <div class="product-grid" id="products-container">
                <!-- Products will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
            
            <div id="pagination" class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;"></div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container">
            <h2>Join Our Premium Club</h2>
            <p>Subscribe to get exclusive early access to our latest drops, special promotions, and style guides.</p>
            <form class="newsletter-form" id="newsletter-form">
                <input type="email" placeholder="Enter your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

<?php require_once __DIR__ . "/components/footer.php"; ?>
