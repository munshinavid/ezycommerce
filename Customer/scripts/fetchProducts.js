document.addEventListener("DOMContentLoaded", () => {
  const productContainer = document.querySelector(".products");
  const loader = document.getElementById("loader");
  let currentPage = 1;
  let loading = false;

  // Async function to fetch products from server
  async function fetchProducts() {
    if (loading) return; // Prevent multiple simultaneous requests
    loading = true;
    loader.style.display = "block"; // Show loading indicator

    try {
      // Fetch product data from PHP backend with current page number
      const response = await fetch(`/ezycommerce/Customer/controllers/ProductController.php?action=fetchProducts&page=${currentPage}`);

      // Check if response is OK (status 200-299)
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      // Parse JSON data from response
      const data = await response.json();

      // Check if we received products data and it has items
      if (data.data && data.data.length > 0) {
        // For each product, create HTML elements and append to container
        data.data.forEach(product => {
          const productElement = document.createElement("article");
          productElement.className = "product card";
          productElement.innerHTML = `
            <div class="badge">
              <span>${product.stock} in stock</span>
              <hr class="hr-design" />
              <span>${product.sold || 0} sold</span>
            </div>
            <img class="product__img" src="..${product.image_url}" alt="${product.name}" />
            <div class="product__body">
              <h3 class="product__name">
                <a href="../views/product.php?id=${product.name}">${product.name}</a>
              </h3>
              <p class="product__category">Category: ${product.category || "N/A"}</p>
              <h4 class="product__price">TK ${product.price}</h4>
              <p class="product__rating"><i class="fa-solid fa-star"></i> ${product.rating || "N/A"}</p>
              <p class="add_cart_confirmation"></p>
              <a href="#" class="product__button add-to-cart" data-product-id="${product.product_id}">
                Add to Cart
              </a>
            </div>
          `;
          productContainer.appendChild(productElement);
        });
        currentPage++; // Increment page for next fetch
      } else {
        // No more products to load, show message and remove scroll listener
        loader.innerHTML = "No more products available.";
        loader.style.display = "none";
        window.removeEventListener("scroll", handleScroll);
      }
    } catch (error) {
      // Handle network or parsing errors
      console.error("Error fetching products:", error);
    } finally {
      // Hide loader and reset loading flag
      loader.style.display = "none";
      loading = false;
    }
  }

  // Event delegation: Listen for clicks on Add to Cart buttons
  document.addEventListener("click", async (event) => {
    if (event.target && event.target.classList.contains("add-to-cart")) {
      event.preventDefault();  // Prevent default anchor click behavior
      const productId = event.target.getAttribute("data-product-id");
      const message = event.target.parentElement.querySelector(".add_cart_confirmation");

      try {
        // Send request to add product to cart on backend
        const response = await fetch(`/ezycommerce/Customer/controllers/CartController.php?action=addToCart&product_id=${productId}`);

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
          message.innerHTML = "Product added to cart!";
          // You can update cart UI here if needed (e.g., cart count badge)
        } else {
          message.innerHTML = "Failed to add product to cart.";
        }
      } catch (error) {
        console.error("Network error while adding product to cart.", error);
        message.innerHTML = "Network error. Please try again.";
      }
    }
  });

  // Scroll event handler to detect when user nears bottom of page
  function handleScroll() {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 50) {
      fetchProducts(); // Load more products
    }
  }

  // Initial product load
  fetchProducts();

  // Attach scroll event listener to window
  window.addEventListener("scroll", handleScroll);
});
