<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$isLoggedIn = isset($_SESSION['user']) && isset($_SESSION['user']['id']);
$username = $isLoggedIn ? ($_SESSION['user']['username'] ?? 'Account') : '';
?>

<!-- navbar starts here -->
<i class="fas fa-bars fa-2x" id="menu-icon"></i>
<nav id="menu" class="hidden">

  <ul class="nav-lower flex-space-around">
    <li class="nav__list">
      <a href="../views/index.php" class="nav__brand nav__link">Navid Express</a>
    </li>
    <li class="nav__list">
      <?php if ($isLoggedIn): ?>
        <a href="../views/cart.php" class="nav__link">
          <span><i class="fa-solid fa-cart-shopping"></i> </span>
          Cart (<?php echo $_SESSION['cart_count'] ?? 0; ?> items - $<?php echo $_SESSION['cart_total'] ?? '0.00'; ?>)
        </a>
      <?php else: ?>
        <a href="../views/login.php" class="nav__link">
          <span><i class="fa-solid fa-cart-shopping"></i> </span>
          Cart (0 items - $0.00)
        </a>
      <?php endif; ?>
    </li>
  </ul>

  <ul class="nav-upper flex-space-around">
    <li class="nav__list">
      <a href="../views/index.php" class="nav__link">Home</a>
    </li>

    <?php if (!$isLoggedIn): ?>
      <!-- Show Register and Login links if not logged in -->
      <li class="nav__list">
        <a href="../views/register.php" class="nav__link">Register</a>
      </li>
      <li class="nav__list">
        <a href="../views/login.php" class="nav__link">Login</a>
      </li>
    <?php else: ?>
      <!-- Show Logout link and Welcome message if logged in -->
      <li class="nav__list">
        <a href="../views/logout.php" class="nav__link">Logout</a>
      </li>
      <li class="nav__list">
        <a href="../views/profile.php" class="nav__link">Welcome, <?php echo htmlspecialchars($username); ?></a>
      </li>
    <?php endif; ?>

    <li class="nav__list">
      <a href="../views/contact.php" class="nav__link">Contact</a>
    </li>

    <li class="nav__list">
      <a href="../views/profile.php" class="nav__link">
        <img class="profile-icon" src="../images/profile1.png" alt="profile icon" />
      </a>
    </li>
  </ul>

</nav>
<!-- navbar ends here -->

    <!-- navbar ends here  -->