<?php
$pageTitle = 'Product Details — EzyCommerce';
$pageDescription = 'View product details, reviews, and add to cart at EzyCommerce.';
require_once __DIR__ . "/components/header.php";

$productId = $_GET['id'] ?? null;
?>

<style>
    .product-detail { max-width: 1300px; margin: 40px auto; padding: 0 24px; }
    .breadcrumb { padding: 16px 0; background: transparent; border: none; }
    .breadcrumb ul { display: flex; list-style: none; gap: 8px; margin: 0; padding: 0; }
    .breadcrumb li { color: #636E72; font-size: 14px; font-weight: 500; }
    .breadcrumb li:not(:last-child)::after { content: '\203A'; margin-left: 8px; color: #B2BEC3; }
    .breadcrumb li:last-child { color: #2D3436; font-weight: 600; }
    .breadcrumb a { color: #636E72; text-decoration: none; transition: color 0.2s; }
    .breadcrumb a:hover { color: #6C5CE7; }
    
    .product-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; margin-top: 24px; }
    
    .product-gallery { position: relative; }
    .main-image-wrap {
        border-radius: 20px; overflow: hidden; background: #F7F8FC;
        aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
        border: 1px solid #E8ECF1;
    }
    .main-image-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-thumbs { display: flex; gap: 10px; margin-top: 14px; }
    .gallery-thumb {
        width: 80px; height: 80px; border-radius: 12px; overflow: hidden;
        border: 2px solid #E8ECF1; cursor: pointer; transition: all 0.2s;
    }
    .gallery-thumb:hover, .gallery-thumb.active { border-color: #6C5CE7; }
    .gallery-thumb img { width: 100%; height: 100%; object-fit: cover; }
    
    .product-details-info {}
    .product-category-tag {
        display: inline-block; padding: 5px 14px; border-radius: 9999px;
        background: linear-gradient(135deg, rgba(108,92,231,0.1), rgba(162,155,254,0.1));
        color: #6C5CE7; font-size: 12px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; margin-bottom: 12px;
    }
    .product-main-title { font-size: 2rem; font-weight: 800; color: #2D3436; margin-bottom: 10px; letter-spacing: -0.5px; line-height: 1.2; }
    
    .product-rating-wrap { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .stars { color: #FDCB6E; font-size: 14px; }
    .rating-text { color: #636E72; font-size: 14px; }
    
    .product-price-block { margin-bottom: 24px; }
    .price-current { font-size: 2rem; font-weight: 900; color: #2D3436; }
    .price-original { font-size: 1.1rem; color: #B2BEC3; text-decoration: line-through; margin-left: 12px; }
    .price-discount {
        display: inline-block; padding: 4px 12px; background: #FFF0F0; color: #FF6B6B;
        border-radius: 9999px; font-size: 13px; font-weight: 700; margin-left: 12px;
    }
    
    .product-description { color: #636E72; line-height: 1.7; margin-bottom: 28px; font-size: 15px; }
    
    .product-options { margin-bottom: 28px; }
    .option-label { font-weight: 700; font-size: 14px; margin-bottom: 10px; display: block; color: #2D3436; }
    .option-values { display: flex; gap: 8px; flex-wrap: wrap; }
    .option-btn {
        padding: 10px 20px; border-radius: 10px; border: 1.5px solid #E8ECF1;
        background: #fff; cursor: pointer; font-weight: 600; font-size: 14px;
        transition: all 0.2s; font-family: 'Inter', sans-serif;
    }
    .option-btn:hover, .option-btn.active { border-color: #6C5CE7; color: #6C5CE7; background: rgba(108,92,231,0.04); }
    
    .add-to-cart-section { display: flex; gap: 12px; align-items: center; margin-bottom: 28px; }
    .quantity-selector {
        display: flex; border: 1.5px solid #E8ECF1; border-radius: 12px; overflow: hidden;
    }
    .quantity-selector button {
        width: 44px; height: 48px; border: none; background: #F7F8FC;
        font-size: 18px; cursor: pointer; color: #2D3436; transition: all 0.2s;
    }
    .quantity-selector button:hover { background: #E8ECF1; color: #6C5CE7; }
    .quantity-selector input {
        width: 56px; border: none; text-align: center; font-size: 16px; font-weight: 700;
        background: transparent; font-family: 'Inter', sans-serif;
    }
    .btn-add-cart {
        flex: 1; padding: 15px 28px; background: linear-gradient(135deg, #6C5CE7, #5A4BD1);
        color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 700;
        cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 15px rgba(108,92,231,0.3);
        font-family: 'Inter', sans-serif;
    }
    .btn-add-cart:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(108,92,231,0.4); }
    .btn-add-wishlist {
        width: 48px; height: 48px; border-radius: 12px; border: 1.5px solid #E8ECF1;
        background: #fff; cursor: pointer; font-size: 18px; color: #636E72;
        transition: all 0.2s; display: flex; align-items: center; justify-content: center;
    }
    .btn-add-wishlist:hover { color: #FF6B6B; border-color: #FF6B6B; }
    
    .product-meta { padding-top: 24px; border-top: 1px solid #E8ECF1; }
    .meta-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #636E72; font-size: 14px; }
    .meta-item i { width: 18px; color: #6C5CE7; }
    
    @media(max-width: 768px) {
        .product-layout { grid-template-columns: 1fr; gap: 28px; }
        .product-main-title { font-size: 1.5rem; }
        .price-current { font-size: 1.6rem; }
    }
</style>

<section class="product-detail">
    <div class="breadcrumb">
        <ul>
            <li><a href="<?php echo url('/'); ?>">Home</a></li>
            <li><a href="<?php echo url('/'); ?>#products">Products</a></li>
            <li>Product Details</li>
        </ul>
    </div>
    
    <div class="product-layout" id="product-detail-container">
        <div class="product-gallery">
            <div class="main-image-wrap">
                <img id="main-product-image" src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600" alt="Product Image">
            </div>
        </div>
        
        <div class="product-details-info">
            <span class="product-category-tag" id="product-category">Category</span>
            <h1 class="product-main-title" id="product-title">Loading Product...</h1>
            
            <div class="product-rating-wrap">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <span class="rating-text">(4.5) · 128 reviews</span>
            </div>
            
            <div class="product-price-block">
                <span class="price-current" id="product-price">$0.00</span>
                <span class="price-original" id="product-original-price">$0.00</span>
                <span class="price-discount">-20% OFF</span>
            </div>
            
            <p class="product-description" id="product-description">
                Loading product description...
            </p>
            
            <div class="add-to-cart-section">
                <div class="quantity-selector">
                    <button type="button" onclick="changeQty(-1)">−</button>
                    <input type="number" id="product-qty" value="1" min="1" max="10">
                    <button type="button" onclick="changeQty(1)">+</button>
                </div>
                <button class="btn-add-cart" id="add-to-cart-btn">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <button class="btn-add-wishlist" id="add-to-wishlist-btn" aria-label="Add to Wishlist">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            
            <div class="product-meta">
                <div class="meta-item"><i class="fas fa-check-circle"></i> In Stock & Ready to Ship</div>
                <div class="meta-item"><i class="fas fa-truck"></i> Free shipping on orders over $50</div>
                <div class="meta-item"><i class="fas fa-undo-alt"></i> 30-day return policy</div>
                <div class="meta-item"><i class="fas fa-shield-alt"></i> Secure checkout guaranteed</div>
            </div>
        </div>
    </div>
</section>

<script>
function changeQty(delta) {
    const input = document.getElementById('product-qty');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 10) val = 10;
    input.value = val;
}
</script>

<?php require_once __DIR__ . "/components/footer.php"; ?>
