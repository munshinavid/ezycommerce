<?php
  session_start();
  include_once '../models/UserModel.php';
  if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
  }

  $userController = new UserModel();
  $user = $userController->getUserById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard</title>
  <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
  <div class="dashboard">
    <!-- Header -->
    <header class="header">
      <h1>Welcome, <span id="customer-name">John Doe</span>!</h1>
      <nav>
        <a href="#">Home</a>
        <a href="#">Shop</a>
        <a href="#">Logout</a>
      </nav>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
      <ul>
        <li><a href="#" class="active">Dashboard</a></li>
        <li><a href="#">Orders</a></li>
        <li><a href="#">Wishlist</a></li>
        <li><a href="#">Addresses</a></li>
        <li><a href="#">Settings</a></li>
        <li><a href="#">Support</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Personal Information Section -->
      <section class="personal-info">
        <h2>Personal Information</h2>
        <div class="info-card">
        <p><strong>Name:</strong> <span id="info-name"><?= $user['username'] ?></span></p>
            <p><strong>Email:</strong> <span id="info-email"><?= $user['email'] ?></span></p>
            <p><strong>Phone:</strong> <span id="info-phone">Khulna, Kushtia</span></p>
            <p><strong>Address:</strong> <span id="info-address">Dhaka, Bangladesh</span></p>
            <a href="update_profile.php" class="edit-btn">Edit Profile</a>

        </div>
      </section>

      <!-- Order History Section -->
      <section class="order-history" id="order-history-section">
        <h2>Order History</h2>
        <table>
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Date</th>
              <th>Total</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="order-list">
            <tr>
              <td>#12345</td>
              <td>2023-10-01</td>
              <td>$99.99</td>
              <td>Delivered</td>
              <td>
                <button class="action-btn" onclick="showOrderDetails(12345)">View Details</button>
              </td>
            </tr>
            <tr>
              <td>#12346</td>
              <td>2023-10-05</td>
              <td>$49.99</td>
              <td>Shipped</td>
              <td>
                <button class="action-btn" onclick="showOrderDetails(12346)">View Details</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
      <section class="order-items" id="order-items-section" style="display: none;">
        <h2>Order Items</h2>
        <table>
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody id="order-items-list">
            <!-- Dynamic items will be inserted here via JS -->
          </tbody>
        </table>
        <button onclick="backToOrders()">Back to Orders</button>
      </section>
    </main>
  </div>

  <!-- Edit Profile Modal -->
  <div id="edit-profile-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <h2>Edit Profile</h2>
      <label>Name: <input type="text" id="edit-name"></label><br>
      <label>Email: <input type="email" id="edit-email"></label><br>
      <label>Phone: <input type="text" id="edit-phone"></label><br>
      <button onclick="updateProfile()">Save</button>
      <button onclick="closeEditForm()">Cancel</button>
    </div>
  </div>

  <!-- Order Details Modal -->
  <div id="order-details-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <h2>Order Details</h2>
      <p><strong>Order ID:</strong> <span id="order-id"></span></p>
      <p><strong>Total:</strong> <span id="order-total"></span></p>
      <p><strong>Status:</strong> <span id="order-status"></span></p>
      <h3>Items:</h3>
      <ul id="order-items"></ul>
      <button onclick="closeOrderDetails()">Close</button>
    </div>
  </div>

  <script src="../scripts/profile.js"></script>
</body>
</html>
