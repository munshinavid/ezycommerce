document.addEventListener('DOMContentLoaded', () => {
    fetchCart();
});

// Fetch Cart Items via AJAX
function fetchCart() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '/Web_project/Customer/controllers/CartController.php?action=fetchCart', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                renderCartItems(response.cartItems);
                updatePaymentSummary(response.subtotal, response.shippingCost, response.totalCost);
            } else {
                alert('Failed to load cart');
            }
        }
    };
    xhr.send();
}

// Render Cart Items Dynamically
function renderCartItems(cartItems) {
    const container = document.getElementById('cart-items-container');
    const itemCount = document.getElementById('item-count');
    container.innerHTML = ''; // Clear previous items
    cartItems.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.classList.add('cart__item', 'card', 'flex-space-around');
        itemElement.setAttribute("data-id", item.cart_item_id);
        itemElement.innerHTML = `
            <input type="checkbox" name="cart_item" />
            <img src="../${item.image_url}" alt="${item.name}" class="cart__item-img" />
            <div class="cart__item-description">
                <h3 class="product__name">${item.name}</h3>
                <h4 class="product__price">Price: TK ${item.price}</h4>
            </div>
            <div class="cart__item-actions">
                <button class="btn" onclick="removeItem(${item.cart_item_id})"><i class="fas fa-trash-alt"></i></button>
                <div>
                    <button class="btn" onclick="updateQuantity(${item.cart_item_id}, 1)"><i class="fas fa-plus"></i></button>
                    <span>${item.quantity}</span>
                    <button class="btn" onclick="updateQuantity(${item.cart_item_id}, -1)"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        `;
        container.appendChild(itemElement);
    });
    itemCount.textContent = `[${cartItems.length} items]`;
}

// Update Payment Summary
function updatePaymentSummary(subtotal, shippingCost, totalCost) {
    document.getElementById('subtotal').textContent = `TK ${subtotal}`;
    document.getElementById('shipping-cost').textContent = `TK ${shippingCost}`;
    document.getElementById('total-cost').textContent = `TK ${totalCost}`;
}

// Remove Item from Cart
function removeItem(cartItemId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/Web_project/Customer/controllers/CartController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                fetchCart(); // Refresh cart
            } else {
                alert('Failed to remove item');
            }
        }
    };
    xhr.send(`action=removeFromCart&cart_item_id=${cartItemId}`);
}

// Update Quantity
function updateQuantity(cartItemId, change) {
    const itemElement = document.querySelector(`[data-id="${cartItemId}"]`);
    let currentQuantity = parseInt(itemElement.querySelector("span").textContent);
    let newQuantity = currentQuantity + change;
    if (newQuantity < 1) return;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/Web_project/Customer/controllers/CartController.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            fetchCart();
        }
    };
    xhr.send(`action=updateQuantity&cart_item_id=${cartItemId}&quantity=${newQuantity}`);
}
