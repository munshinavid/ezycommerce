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
          ></marquee
        >
        
      </div>
</header>
    <!-- header ends here  -->
    


        <div class="actions__search">
          <input type="text" id="searchInput" placeholder="Search product" />
          <button class="btn">
            <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
          </button>
       </div>

        <!-- product section starts here -->
        <section class="products">
          <!-- Products will be dynamically appended here via JavaScript -->
        </section>
        <div id="loader" class="loader" style="display: none;">Loading...</div>




        
        <!-- product section ends here  -->
      </div>
    </main>
    <!-- footer starts here  -->
    <?php include '../layout/footer.php'; ?>
    <!-- footer ends here  -->

    <script src="../scripts/index.js"></script>
    <script src="../scripts/fetchProducts.js"></script>
    <script src="../scripts/search.js"></script>
  </body>
</html>
