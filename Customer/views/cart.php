<?php
// Use minimal header (logo + user info + logout only)
$minimalHeader = true;
$pageTitle = 'Shopping Cart — EzyCommerce';
$extraCss = [url('/Customer/css/cart.css')];
require_once __DIR__ . "/components/header.php";
?>


    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <ul>
            <li><a href="<?php echo url('/'); ?>">Home</a></li>
            <li>Shopping Cart</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Cart Items -->
        <div class="cart-container">
            <div class="cart-header">
                <h1 class="cart-title">Your Shopping Cart</h1>
                <span id="cart-item-count">3 items</span>
            </div>
            
            <div class="cart-items" id="cart-items">
                <!-- Cart items will be loaded dynamically -->
            </div>

            <!-- Empty cart state (hidden by default) -->
            <div class="empty-cart" id="empty-cart" style="display: none;">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="<?php echo url('/'); ?>" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h2 class="summary-title">Order Summary</h2>
            
            <div class="summary-row">
                <span>Subtotal (3 items)</span>
                <span id="subtotal">$359.97</span>
            </div>
            
            <div class="summary-row">
                <span>Shipping</span>
                <span id="shipping">$5.99</span>
            </div>
            
            <div class="summary-row">
                <span>Tax</span>
                <span id="tax">$28.80</span>
            </div>
            
            <div class="summary-row">
                <span>Discount</span>
                <span id="discount">-$50.00</span>
            </div>
            
            <div class="summary-row summary-total">
                <span>Total</span>
                <span id="total">$344.76</span>
            </div>
            
            <div class="discount-form">
                <div class="discount-input">
                    <input type="text" placeholder="Discount code">
                    <button class="apply-btn">Apply</button>
                </div>
            </div>
            
            <button class="checkout-btn">Proceed to Checkout</button>
            <a href="<?php echo url('/'); ?>" class="continue-shopping">Continue Shopping</a>
        </div>
    </div>
    <!-- Checkout Modal -->
    <div id="checkout-modal" class="checkout-modal hidden">
        <div class="checkout-modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Complete Your Order</h2>
                <button type="button" class="close-btn" aria-label="Close">&times;</button>
            </div>

            <div class="modal-section">
                <h3 class="section-heading">Select Shipping Address</h3>
                <div class="address-toolbar">
                    <button type="button" class="btn btn-secondary" id="refresh-addresses-btn">Refresh</button>
                    <button type="button" class="btn btn-primary" id="add-address-btn">Add New Address</button>
                </div>
                <div id="address-list" class="address-list">
                    <!-- Address cards will be injected here -->
                </div>
                <div id="address-error" class="form-error" style="display:none;"></div>
            </div>

            <div class="modal-section">
                <h3 class="section-heading">Payment Method</h3>
                <div class="payment-options">
                    <label class="radio-option"><input type="radio" name="payment_method" value="Cash on Delivery" checked> Cash on Delivery</label>
                    <label class="radio-option"><input type="radio" name="payment_method" value="Credit Card"> Credit Card</label>
                    <label class="radio-option"><input type="radio" name="payment_method" value="Mobile Banking"> Mobile Banking</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancel-checkout-btn">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-order-btn" disabled>Place Order</button>
            </div>
        </div>
    </div>


    <!-- Recently Viewed -->
    <div class="recently-viewed">
        <h2 class="section-title">Recently Viewed</h2>
        <div class="products">
            <!-- Product 1 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Wireless Earbuds" class="product-image">
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">
                        <span class="current-price">$79.99</span>
                        <span class="original-price">$99.99</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-cart">Add to Cart</button>
                    </div>
                </div>
            </div>
            
            <!-- Product 2 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1526947425960-945c6e72858f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Backpack" class="product-image">
                <div class="product-info">
                    <h3 class="product-title">Travel Backpack</h3>
                    <div class="product-price">
                        <span class="current-price">$49.99</span>
                        <span class="original-price">$69.99</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-cart">Add to Cart</button>
                    </div>
                </div>
            </div>
            
            <!-- Product 3 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Smart Speaker" class="product-image">
                <div class="product-info">
                    <h3 class="product-title">Smart Speaker</h3>
                    <div class="product-price">
                        <span class="current-price">$129.99</span>
                        <span class="original-price">$159.99</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-cart">Add to Cart</button>
                    </div>
                </div>
            </div>
            
            <!-- Product 4 -->
            <div class="product-card">
                <img src="https://images.unsplash.com/photo-1585386959984-a4155224a1ad?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Perfume" class="product-image">
                <div class="product-info">
                    <h3 class="product-title">Luxury Perfume</h3>
                    <div class="product-price">
                        <span class="current-price">$59.99</span>
                        <span class="original-price">$79.99</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-cart">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>EzyCommerce</h3>
                    <p>Your one-stop destination for all your shopping needs. Quality products at affordable prices.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="<?php echo url('/'); ?>">Home</a></li>
                        <li><a href="<?php echo url('/cart'); ?>">Cart</a></li>
                        <li><a href="<?php echo url('/wishlist'); ?>">Wishlist</a></li>
                        <li><a href="<?php echo url('/profile'); ?>">My Account</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="<?php echo url('/contact'); ?>">Contact Us</a></li>
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Commerce St, City, State 12345</li>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> support@ezycommerce.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 EzyCommerce. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript Files -->
    <script src="<?php echo url('/Customer/scripts/cart.js'); ?>"></script>
</body>
</html>