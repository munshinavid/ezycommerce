document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const productContainer = document.querySelector(".products");
  
    searchInput.addEventListener("keyup", function () {
      let query = searchInput.value.trim().toLowerCase();
      
      const products = document.querySelectorAll(".product.card"); // Select all dynamically loaded products
  
      products.forEach((product) => {
        const productName = product.querySelector(".product__name a").innerText.toLowerCase();
        const productCategory = product.querySelector(".product__category").innerText.toLowerCase();
  
        if (productName.includes(query) || productCategory.includes(query)) {
          product.style.display = "block";
        } else {
          product.style.display = "none";
        }
      });
    });
  });
  