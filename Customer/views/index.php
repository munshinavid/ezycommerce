<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="this is an ecommerce project for making anis express"
    />
    <title>Ecommerce project</title>

    <!-- font awesome cdn  -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
      integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/nav.css" />
    <link rel="stylesheet" href="../css/footer.css" />
  </head>
  <body>



  
    
<!-- Nav bar starts here -->
<?php include '../layout/navbar.php'; ?>
    

    
<!-- header starts here  -->
<header class="header">
      <div class="banner flex-space-around">
        <marquee class="banner__title" behavior="" direction=""
          >50% Sales going on</marquee
        >
        <div class="features flex-space-around">
          <article class="feature flex-center">
            <i class="fa-solid fa-truck feature__icon"></i>
            <h3>Shipping within 7 days</h3>
          </article>
          <article class="feature flex-center">
            <i class="fa-brands fa-rocketchat feature__icon"></i>
            <h3>Support 24/7</h3>
          </article>
          <article class="feature flex-center">
            <i class="fa-regular fa-credit-card feature__icon"></i>
            <h3>Safe Payment</h3>
          </article>
        </div>
      </div>
    </header>
    <!-- header ends here  -->
    

    <main class="flex-center">
      <div class="sidebar">
        <section class="categories-section">
          <h3 class="section-title">Categories</h3>
          <ul class="list">
            <li class="list__item">
              <label for="phone">
                <input type="checkbox" name="phone" id="phone" value="phone" />
                Phone
              </label>
            </li>
            <li class="list__item">
              <label for="tablets">
                <input
                  type="checkbox"
                  name="tablets"
                  id="tablets"
                  value="tablets"
                />
                Tablets
              </label>
            </li>
            <li class="list__item">
              <label for="telivisions">
                <input
                  type="checkbox"
                  name="telivisions"
                  id="telivisions"
                  value="telivisions"
                />
                Telivisions
              </label>
            </li>

            <li class="list__item">
              <label for="computers">
                <input
                  type="checkbox"
                  name="computers"
                  id="computers"
                  value="computers"
                />
                Computers
              </label>
            </li>

            <li class="list__item">
              <label for="headphones">
                <input
                  type="checkbox"
                  name="headphones"
                  id="headphones"
                  value="headphones"
                />
                Headphones
              </label>
            </li>
          </ul>
        </section>

        <section class="price-section">
          <h3 class="section-title">Price Range</h3>
          <ul class="list">
            <li class="list__item">
              <label for="price1">
                <input type="radio" name="price" id="price1" value="[0,20]" />
                $0-$20
              </label>
            </li>
            <li class="list__item">
              <label for="price2">
                <input type="radio" name="price" id="price2" value="[21,50]" />
                $21-$50
              </label>
            </li>
            <li class="list__item">
              <label for="price3">
                <input type="radio" name="price" id="price3" value="[51,100]" />
                $51-$100
              </label>
            </li>
            <li class="list__item">
              <label for="price4">
                <input
                  type="radio"
                  name="price"
                  id="price4"
                  value="[101,500]"
                />
                $101-$500
              </label>
            </li>
            <li class="list__item">
              <label for="price5">
                <input type="radio" name="price" id="price5" value="[501+]" />
                $501+
              </label>
            </li>
          </ul>
        </section>

        <section class="shippinge-section">
          <h3 class="section-title">Shipping Options</h3>
          <ul class="list">
            <li class="list__item">
              <label for="free">
                <input type="radio" name="shipping" id="free" value="free" />
                Free
              </label>
            </li>
            <li class="list__item">
              <label for="paid">
                <input type="radio" name="shipping" id="paid" value="paid" />
                Paid
              </label>
            </li>
          </ul>
        </section>
      </div>
      <div class="main-content">
        <section class="actions flex-space-around">
          <div class="actions__sort">
            <label for="sort">Sort By: </label>
            <select name="sort" id="sort">
              <option value="sold">Most sold first</option>
              <option value="rating">Most rated first</option>
              <option value="price">Cheapest first</option>
              <option value="arrival">Newest first</option>
              <option value="discount">Biggest discount first</option>
            </select>
          </div>

          <div class="actions__search">
            <input type="text" placeholder="Search product" />
            <button class="btn">
              <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
            </button>
          </div>
        </section>

        <!-- product section starts here -->
        <section class="products">
          <!-- Products will be dynamically appended here via JavaScript -->
        </section>
        <div id="loader" class="loader" style="display: none;">Loading...</div>




        <div class="pagination">
          <button class="btn pagination__btn">
            <i class="fas fa-less-than"></i>
          </button>
          <button class="btn pagination__btn">2</button>
          <button class="btn pagination__btn">3</button>
          <button class="btn pagination__btn">4</button>
          <button class="btn pagination__btn">5</button>
          <button class="btn pagination__btn">6</button>
          <button class="btn pagination__btn">7</button>
          <button class="btn pagination__btn">8</button>
          <button class="btn pagination__btn">9</button>
          <button class="btn pagination__btn">10</button>
          <button class="btn pagination__btn">...</button>
          <button class="btn pagination__btn">50</button>
          <button class="btn pagination__btn">
            <i class="fas fa-greater-than"></i>
          </button>
        </div>
        <!-- product section ends here  -->
      </div>
    </main>
    <!-- footer starts here  -->
    <?php include '../layout/footer.php'; ?>
    <!-- footer ends here  -->

    <script src="../scripts/index.js"></script>
    <script src="../scripts/fetchProducts.js"></script>
  </body>
</html>
