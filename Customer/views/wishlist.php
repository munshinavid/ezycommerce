<?php require_once __DIR__ . '/components/header.php'; ?>

<main>
    <section class="products-section" style="padding-top: 60px;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Your Wishlist</h2>
                <a href="<?php echo url('/'); ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>

            <div id="wishlist-empty" class="empty-state" style="display:none; text-align:center; padding: 40px 0;">
                Your wishlist is empty. <a href="<?php echo url('/'); ?>">Start shopping!</a>
            </div>

            <div id="wishlist-container" class="product-grid">
                <!-- Products will load here -->
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/components/footer.php'; ?>

<script src="<?php echo url('/Customer/scripts/home.js'); ?>"></script>
<script>
    const currentUser = JSON.parse(localStorage.getItem('userData'));
    const currentUserId = currentUser ? currentUser.id : null;

    function renderWishlistItems(items) {
        const container = document.getElementById('wishlist-container');
        const empty = document.getElementById('wishlist-empty');

        if (!container || !empty) {
            return;
        }

        if (!items || items.length === 0) {
            container.innerHTML = '';
            empty.style.display = 'block';
            return;
        }

        empty.style.display = 'none';
        container.innerHTML = items.map(item => `
            <div class="product-card" data-product-id="${item.product_id}">
                <div class="product-image-wrap">
                    <img src="${item.image_url || 'https://via.placeholder.com/300x200?text=No+Image'}" alt="${item.name}" class="product-image">
                </div>
                <div class="product-info">
                    <div class="product-category">${item.category_name || 'Wishlist'}</div>
                    <h3 class="product-title">${item.name}</h3>
                    <div class="product-price">
                        <span class="current-price">$${Number(item.price || 0).toFixed(2)}</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-cart" type="button" onclick="addItemToCart(${item.product_id})">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn-wishlist in-wishlist" type="button" onclick="removeFromWishlist(${item.product_id})">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async function loadWishlistPage() {
        if (!currentUserId) {
            const container = document.getElementById('wishlist-container');
            const empty = document.getElementById('wishlist-empty');

            if (container && empty) {
                container.innerHTML = '';
                empty.innerHTML = 'Please <a href="<?php echo url('/login'); ?>">log in</a> to view your wishlist.';
                empty.style.display = 'block';
            }
            return;
        }

        try {
            const items = await window.ecommerceAPI.getWishlistItems();
            renderWishlistItems(items);
        } catch (error) {
            console.error('Failed to load wishlist items:', error);
            if (typeof showToast === 'function') {
                showToast('Failed to load wishlist. Please refresh.', 'error');
            }
        }
    }

    async function addItemToCart(productId) {
        if (!currentUserId) {
            window.location.href = 'login.php';
            return;
        }

        await window.ecommerceAPI.addToCart(productId);
    }

    async function removeFromWishlist(productId) {
        if (!currentUserId) {
            window.location.href = 'login.php';
            return;
        }

        try {
            const response = await window.ecommerceAPI.apiCall(`users/${currentUserId}/wishlist/${productId}`, {
                method: 'DELETE'
            });

            if (response.success) {
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Removed from wishlist', 'success');
                }
                loadWishlistPage();
            } else if (typeof showToast === 'function') {
                showToast(response.error || 'Failed to remove item', 'error');
            }
        } catch (error) {
            console.error('Failed to remove wishlist item:', error);
            if (typeof showToast === 'function') {
                showToast('Failed to remove item from wishlist.', 'error');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadWishlistPage);
</script>
