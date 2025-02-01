const menuIcon = document.getElementById("menu-icon");
const menu = document.getElementById("menu");

menuIcon.addEventListener("click", () => {
  if (menu.className === "hidden") {
    menu.classList.remove("hidden");
  } else {
    menu.classList.add("hidden");
  }
});

let prevScrollPos = window.pageYOffset;
const navbar = document.querySelector('nav');

window.addEventListener('scroll', () => {
  let currentScrollPos = window.pageYOffset;

  if (prevScrollPos > currentScrollPos) {
    // Scrolling up - show the navbar
    navbar.classList.remove('hidden');
  } else {
    // Scrolling down - hide the navbar
    navbar.classList.add('hidden');
  }

  prevScrollPos = currentScrollPos;
});

const productContainer = document.getElementById("product-container");

  fetch("fetch_products.php")
    .then((response) => response.json())
    .then((products) => {
      products.forEach((product) => {
        const productCard = `
          <article class="product card">
            <div class="badge">
              <span>${product.stock} in stock</span>
            </div>
            <img
              class="product__img"
              src="${product.image_url}"
              alt="${product.name}"
            />
            <div class="product__body">
              <h3 class="product__name">
                <a href="product_details.php?id=${product.id}">${product.name}</a>
              </h3>
              <p class="product__description">${product.description}</p>
              <h4 class="product__price">$${product.price}</h4>
              <p class="product__rating">Rating: ${product.rating}/5</p>
              <button class="btn product__button">Add To Cart</button>
            </div>
          </article>
        `;
        productContainer.innerHTML += productCard;
      });
    })
    .catch((error) => console.error("Error fetching products:", error));

