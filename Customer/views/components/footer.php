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
                    <ul class="footer-links">
                        <li><a href="<?php echo url('/'); ?>">Home</a></li>
                        <li><a href="<?php echo url('/cart'); ?>">Cart</a></li>
                        <li><a href="<?php echo url('/wishlist'); ?>">Wishlist</a></li>
                        <li><a href="<?php echo url('/profile'); ?>">My Account</a></li>
                        <li><a href="<?php echo url('/login'); ?>">Login</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo url('/contact'); ?>">Contact</a></li>
                        <li><a href="<?php echo url('/cart'); ?>">Review Cart</a></li>
                        <li><a href="<?php echo url('/wishlist'); ?>">View Wishlist</a></li>
                        <li><a href="<?php echo url('/profile'); ?>">Order History</a></li>
                        <li><a href="<?php echo url('/login'); ?>">Account Help</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul class="footer-links">
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

    <!-- Quick View Modal -->
    <div class="modal" id="quick-view-modal">
        <div class="modal-content">
            <span class="close-modal" id="close-modal">&times;</span>
            <div class="quick-view-content" id="quick-view-content">
                <!-- Quick view content will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <script src="<?php echo url('/Customer/scripts/home.js'); ?>"></script>
</body>
</html>
