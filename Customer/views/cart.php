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
  </head>
  <body>
    <!-- navbar starts here  -->
    
    <!-- navbar ends here  -->
    <main>
      <div class="cart flex-center">
        <div class="cart__items">
          <!-- cart item will dynamically load here -->
        </div>
        <div class="cart__payment">
          <div class="cart__payment-summary card">
            <h2>Payment Summary</h2>
            <div>
              <p>subtotal:</p>
              <p>$341.44</p>
            </div>
            <div>
              <p>Shipping Cost:</p>
              <p>$2.50</p>
            </div>
            <div>
              <p>Total Cost:</p>
              <p>$344.38</p>
            </div>
            <button class="btn cart__payment-btn">Pay Now</button>
          </div>
          <div class="cart__payment-methods card">
            <h2>Payment Methods</h2>
            <div>
              <i class="fa-brands fa-cc-visa fa-3x"></i>
              <i class="fa-brands fa-cc-apple-pay fa-3x"></i>
              <i class="fa-brands fa-cc-amex fa-3x"></i>
              <i class="fa-brands fa-cc-amazon-pay fa-3x"></i>
              <i class="fa-brands fa-cc-paypal fa-3x"></i>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- footer starts here  -->
    
    <!-- footer ends here  -->
    <script src="../scripts/cart.js"></script>
  </body>
</html>
