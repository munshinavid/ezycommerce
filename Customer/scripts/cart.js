document.addEventListener("DOMContentLoaded", function () {
    fetchCartItems();

    function fetchCartItems() {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "../controllers/CartController.php?action=fetchCart", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    updateCartUI(response.cartItems, response.subtotal, response.shippingCost, response.totalCost);
                }
            }
        };
        xhr.send();
    }

    function updateCartUI(cartItems, subtotal, shippingCost, totalCost) {
        let cartContainer = document.querySelector(".cart__items");
        let paymentSummary = document.querySelector(".cart__payment-summary");
        cartContainer.innerHTML = "";
        
        cartItems.forEach(item => {
            let cartItem = document.createElement("div");
            cartItem.classList.add("cart__item", "card", "flex-space-around");
            cartItem.innerHTML = `
                <input type="checkbox" />
                <img src=".${item.image_url}" alt="${item.name}" class="cart__item-img" />
                <div class="cart__item-description">
                    <h3 class="product__name">${item.name}</h3>
                    <h4 class="product__price">Price: $${item.price}</h4>
                    <p class="cart__item-shipping">Shipping cost $2.50</p>
                </div>
                <div class="cart__item-actions">
                    <button class="btn delete-item" data-id="${item.cart_item_id}"><i class="fas fa-trash-alt"></i></button>
                    <div>
                        <button class="btn increase-qty" data-id="${item.cart_item_id}"><i class="fas fa-add"></i></button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="btn decrease-qty" data-id="${item.cart_item_id}"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            `;
            cartContainer.appendChild(cartItem);
        });
        
        paymentSummary.innerHTML = `
            <h2>Payment Summary</h2>
            <div><p>Subtotal:</p><p>$${subtotal}</p></div>
            <div><p>Shipping Cost:</p><p>$${shippingCost}</p></div>
            <div><p>Total Cost:</p><p>$${totalCost}</p></div>
            <button class="btn cart__payment-btn">Pay Now</button>
        `;

        attachEventListeners();
    }

    function attachEventListeners() {
        document.querySelectorAll(".increase-qty").forEach(button => {
            button.addEventListener("click", function () {
                let cartItemId = this.getAttribute("data-id");
                updateCartItem(cartItemId, 1);
            });
        });

        document.querySelectorAll(".decrease-qty").forEach(button => {
            button.addEventListener("click", function () {
                let cartItemId = this.getAttribute("data-id");
                updateCartItem(cartItemId, -1);
            });
        });

        document.querySelectorAll(".delete-item").forEach(button => {
            button.addEventListener("click", function () {
                let cartItemId = this.getAttribute("data-id");
                removeCartItem(cartItemId);
            });
        });
    }

    // Update Quantity
    function updateCartItem(cartItemId, change) {
        const itemElement = document.querySelector(`.increase-qty[data-id="${cartItemId}"]`);
        if (!itemElement) return;

        const quantitySpan = itemElement.parentElement.querySelector(".quantity");
        let currentQuantity = parseInt(quantitySpan.textContent);
        let newQuantity = currentQuantity + change;

        if (newQuantity < 1) return;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `/ezycommerce/Customer/controllers/CartController.php?action=updateQuantity&cart_item_id=${cartItemId}&quantity=${newQuantity}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                fetchCartItems();
            } else {
                console.log('Error updating quantity');
            }
        };
        xhr.send();
    }

    // Remove Cart Item
    function removeCartItem(cartItemId) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `/ezycommerce/Customer/controllers/CartController.php?action=removeFromCart&cart_item_id=${cartItemId}`, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                fetchCartItems();
            }
        };
        xhr.send();
    }
});
