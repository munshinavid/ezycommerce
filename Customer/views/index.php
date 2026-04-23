<?php require_once __DIR__ . "/components/header.php"; ?>
    <!-- Breadcrumbs -->
    <div class="container">
        <div class="breadcrumbs">
            <a href="#">Home</a> / <span>All Products</span>
        </div>
    </div>

    <!-- Hero Section with Discount Banners -->
    <section class="hero">
        <div class="container">
            <div class="discount-banners" id="discount-banners">
                <!-- Discount banners will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges -->
    <div class="container">
        <div class="trust-badges">
            <div class="trust-badge">
                <i class="fas fa-shield-alt"></i>
                <span>Secure Payment</span>
            </div>
            <div class="trust-badge">
                <i class="fas fa-truck"></i>
                <span>Free Shipping</span>
            </div>
            <div class="trust-badge">
                <i class="fas fa-undo-alt"></i>
                <span>30-Day Returns</span>
            </div>
            <div class="trust-badge">
                <i class="fas fa-headset"></i>
                <span>24/7 Support</span>
            </div>
        </div>
    </div>

    <!-- Category Cards -->
    <section class="categories-section">
        <div class="container">
            <div class="section-title">
                <h2>Shop By Category</h2>
            </div>
            <div class="category-grid" id="categories-container">
                <!-- Categories will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Products</h2>
                <div class="product-filters" id="product-filters">
                    <!-- Filter buttons will be loaded here via AJAX -->
                </div>
            </div>
            <div class="product-grid" id="products-container">
                <!-- Products will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
            <div class="load-more">
                <button class="cta-button" id="load-more-btn">Load More Products</button>
            </div>
        </div>
    </section>

    <!-- Recommendations Section -->
    <section class="recommendations-section">
        <div class="container">
            <div class="section-title">
                <h2>You May Also Like</h2>
            </div>
            <div class="recommendations-grid" id="recommendations-container">
                <!-- Recommended products will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>What Our Customers Say</h2>
            </div>
            <div class="testimonials-grid" id="testimonials-container">
                <!-- Testimonials will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container">
            <h2>Subscribe to Our Newsletter</h2>
            <p>Get the latest updates on new products and upcoming sales</p>
            <form class="newsletter-form" id="newsletter-form">
                <input type="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

<?php require_once __DIR__ . "/components/footer.php"; ?>
