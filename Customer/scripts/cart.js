document.addEventListener("DOMContentLoaded", () => {
  // Fetch and display cart items on page load
  fetchCartItems();

  // Fetch cart items from server and update UI
  async function fetchCartItems() {
    try {
      const response = await fetch('/ezycommerce/Customer/controllers/CartController.php?action=fetchCart');
      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
      const data = await response.json();

      if (data.success) {
        updateCartUI(data.cartItems, data.subtotal, data.shippingCost, data.totalCost);
      } else {
        console.error("Failed to fetch cart items");
      }
    } catch (error) {
      console.error("Error fetching cart items:", error);
    }
  }

  // Update cart items UI and payment summary
  function updateCartUI(cartItems, subtotal, shippingCost, totalCost) {
    const cartContainer = document.querySelector(".cart__items");
    const paymentSummary = document.querySelector(".cart__payment-summary");

    // Clear existing cart items
    cartContainer.innerHTML = "";

    // Generate each cart item element dynamically
    cartItems.forEach(item => {
      const cartItem = document.createElement("div");
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
          <button class="btn delete-item" data-id="${item.cart_item_id}">
            <i class="fas fa-trash-alt"></i>
          </button>
          <div>
            <button class="btn increase-qty" data-id="${item.cart_item_id}">
              <i class="fas fa-plus"></i>
            </button>
            <span class="quantity">${item.quantity}</span>
            <button class="btn decrease-qty" data-id="${item.cart_item_id}">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
      `;
      cartContainer.appendChild(cartItem);
    });

    // Update payment summary section dynamically
    paymentSummary.innerHTML = `
      <h2>Payment Summary</h2>
      <div><p>Subtotal:</p><p>$${subtotal}</p></div>
      <div><p>Shipping Cost:</p><p>$${shippingCost}</p></div>
      <div><p>Total Cost:</p><p>$${totalCost}</p></div>
      <button class="btn cart__payment-btn">Pay Now</button>
    `;

    // Attach event listeners after dynamic elements are created
    attachEventListeners();
  }

  // Attach event listeners for qty buttons, delete buttons, and pay now button
  function attachEventListeners() {
    document.querySelectorAll(".increase-qty").forEach(button => {
      button.addEventListener("click", () => {
        const cartItemId = button.getAttribute("data-id");
        updateCartItemQuantity(cartItemId, 1);
      });
    });

    document.querySelectorAll(".decrease-qty").forEach(button => {
      button.addEventListener("click", () => {
        const cartItemId = button.getAttribute("data-id");
        updateCartItemQuantity(cartItemId, -1);
      });
    });

    document.querySelectorAll(".delete-item").forEach(button => {
      button.addEventListener("click", () => {
        const cartItemId = button.getAttribute("data-id");
        removeCartItem(cartItemId);
      });
    });

    const payNowButton = document.querySelector(".cart__payment-btn");
    if (payNowButton) {
      payNowButton.addEventListener("click", () => {
        placeOrder();
      });
    }
  }

  // Update quantity of a cart item on server, then refresh UI
  async function updateCartItemQuantity(cartItemId, change) {
    try {
      // Find current quantity element
      const quantitySpan = document.querySelector(`.increase-qty[data-id="${cartItemId}"]`).parentElement.querySelector(".quantity");
      let currentQuantity = parseInt(quantitySpan.textContent);
      let newQuantity = currentQuantity + change;

      if (newQuantity < 1) return; // Prevent quantity less than 1

      // Send update request to backend
      const response = await fetch(`/ezycommerce/Customer/controllers/CartController.php?action=updateQuantity&cart_item_id=${cartItemId}&quantity=${newQuantity}`);

      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

      // Refresh cart UI after update
      fetchCartItems();
    } catch (error) {
      console.error("Error updating cart item quantity:", error);
    }
  }

  // Remove cart item on server, then refresh UI
  async function removeCartItem(cartItemId) {
    try {
      const response = await fetch(`/ezycommerce/Customer/controllers/CartController.php?action=removeFromCart&cart_item_id=${cartItemId}`);

      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

      fetchCartItems();
    } catch (error) {
      console.error("Error removing cart item:", error);
    }
  }

  // Simulate placing the order, clear cart UI, disable pay button
  async function placeOrder() {
  try {
    // First, fetch cart data again to get the total amount
    const response = await fetch('/ezycommerce/Customer/controllers/CartController.php?action=fetchCart');
    if (!response.ok) throw new Error(`Failed to fetch cart data`);
    const cartData = await response.json();

    if (!cartData.success || cartData.totalCost <= 0) {
      alert("Cannot place order: empty cart or invalid amount.");
      return;
    }

    // Call backend to place order
    const placeOrderResponse = await fetch(`/ezycommerce/Customer/controllers/OrderController.php?action=placeOrder&total_amount=${cartData.totalCost}`);
    alert(cartData.totalCost);
    if (!placeOrderResponse.ok) throw new Error("Failed to place order");

    const result = await placeOrderResponse.json();

    if (result.success) {
      // Show order placed message
      const cartContainer = document.querySelector(".cart__items");
      const paymentSummary = document.querySelector(".cart__payment-summary");
      if (cartContainer) cartContainer.innerHTML = "";

        if (paymentSummary) {
    paymentSummary.innerHTML = `
      <h2>Order Placed Successfully!</h2>
      <p>Thank you for your purchase. Your order is being processed.</p>
      <a href="/ezycommerce/Customer/views/index.php" class="btn-home" style="
        display: inline-block;
        margin-top: 15px;
        padding: 10px 20px;
        background-color: #28a745;
        color: white; 
        text-decoration: none;
        border-radius: 5px;
      ">
        Return to Home
      </a>
    `;
      }
      // Optionally clear cart on backend
      //await clearCart();
    } else {
      alert(result.message || "Order failed.");
    }
  } catch (error) {
    console.error("Error placing order:", error);
    alert("Something went wrong while placing the order.");
  }
}


  // (Optional) Clear cart on backend and refresh UI
  async function clearCart() {
    try {
      const response = await fetch(`/ezycommerce/Customer/controllers/OrderController.php?action=clearCart`);
      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

      fetchCartItems();
    } catch (error) {
      console.error("Error clearing cart:", error);
    }
  }
});
