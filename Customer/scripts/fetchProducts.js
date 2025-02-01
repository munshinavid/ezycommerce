document.addEventListener("DOMContentLoaded", () => {
    const productContainer = document.querySelector(".products");
    const loader = document.getElementById("loader");
    let currentPage = 1;
    let loading = false;

    const fetchProducts = () => {
        if (loading) return;
        loading = true;
        loader.style.display = "block";

        const xhr = new XMLHttpRequest();
        xhr.open("GET", `/Web_project/Customer/controllers/ProductController.php?action=fetchProducts&page=${currentPage}`, true);

        xhr.onload = () => {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.data && response.data.length > 0) {
                    response.data.forEach((product) => {
                        const productElement = document.createElement("article");
                        productElement.className = "product card";
                        productElement.innerHTML = `
                            <div class="badge">
                                <span>${product.stock} in stock</span>
                                <hr class="hr-design" />
                                <span>${product.sold || 0} sold</span>
                            </div>
                            <img class="product__img" src="${product.image_url}" alt="${product.name}" />
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

                    currentPage++;
                } else {
                    loader.innerHTML = "No more products available.";
                    loader.style.display = "none";
                    window.removeEventListener("scroll", handleScroll);
                }
                loading = false;
                loader.style.display = "none";
            } else {
                console.error("Error fetching products:", xhr.statusText);
                loader.style.display = "none";
                loading = false;
            }
        };

        xhr.onerror = () => {
            console.error("Network error occurred.");
            loader.style.display = "none";
            loading = false;
        };

        xhr.send();
    };

    // Add to cart click handler
    document.addEventListener("click", (event) => {
        if (event.target && event.target.classList.contains("add-to-cart")) {
            event.preventDefault();  // Prevent the default link behavior
            const productId = event.target.getAttribute("data-product-id");
            var message = event.target.parentElement.querySelector(".add_cart_confirmation");

            // Make an AJAX request to add the product to the cart
            const cartXhr = new XMLHttpRequest();
            cartXhr.open("GET", `/Web_project/Customer/controllers/CartController.php?action=addToCart&product_id=${productId}`, true);

            cartXhr.onload = () => {
                if (cartXhr.status === 200) {
                    const response = JSON.parse(cartXhr.responseText);
                    if (response.success) {
                        message.innerHTML = "Product added to cart!";
                        //alert("Product added to cart!");
                        // Optionally update the cart UI dynamically, e.g., by showing cart count
                    } else {
                        message.innerHTML = "Failed to add product to cart.";
                    }
                } else {
                    console.error("Error adding product to cart:", cartXhr.statusText);
                }
            };

            cartXhr.onerror = () => {
                console.error("Network error while adding product to cart.");
            };

            cartXhr.send();
        }
    });

    const handleScroll = () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 50) {
            fetchProducts();
        }
    };

    fetchProducts();
    window.addEventListener("scroll", handleScroll);
});
