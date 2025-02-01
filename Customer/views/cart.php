<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart__items-action .btn i {
            font-size: 1.2rem; /* Increased icon size */
            padding: 0.3rem; /* Added padding for better clarity */
        }

        .cart__items-action .btn {
            margin-left: 0.5rem; /* Added spacing between buttons */
        }

        .cart__payment-summary select {
            margin-top: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <?php include '../layout/navbar.php'; ?>
    <main>
        <div class="cart flex-center">
            <div class="cart__items">
                <div class="cart__items-heading card">
                    <h2>Shopping Cart <span id="item-count">[0 items]</span></h2>
                    <div class="cart__items-action">
                        <label for="select">
                            <input type="checkbox" name="select" id="select" />
                            Select all items
                        </label>
                        <button class="btn" id="clear-cart">
                            <i class="fas fa-trash-alt"></i> Clear Cart
                        </button>
                        <button class="btn">Shop More</button>
                    </div>
                </div>

                <div id="cart-items-container">
                    <!-- Cart items will be dynamically loaded here -->
                    <div class="cart-item">
                        <p>Example Item</p>
                        <div class="cart-item-controls">
                            <button class="btn">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                            <span>1</span>
                            <button class="btn">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                            <button class="btn">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart__payment">
                <div class="cart__payment-summary card">
                    <h2>Payment Summary</h2>
                    <div>
                        <p>Subtotal:</p>
                        <p id="subtotal">$0.00</p>
                    </div>
                    <div>
                        <p>Shipping Cost:</p>
                        <p id="shipping-cost">$0.00</p>
                    </div>
                    <div>
                        <p>Total Cost:</p>
                        <p id="total-cost">$0.00</p>
                    </div>
                    <label for="payment-method">Payment Method:</label>
                    <select id="payment-method">
                        <option value="credit-card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="bank-transfer">Bank Transfer</option>
                    </select>
                    <button class="btn cart__payment-btn">Pay Now</button>
                </div>
            </div>
        </div>
    </main>
    <?php include '../layout/footer.php'; ?>

    <script src="../scripts/cart.js"></script>
</body>
</html>
