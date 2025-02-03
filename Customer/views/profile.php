<?php
require_once '../models/OrderModel.php';
require_once '../models/UserModel.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$orderModel = new OrderModel();
$userModel = new UserModel();
$orders = $orderModel->getAllOrders($_SESSION['user_id']); // Fetch all orders
$user = $userModel->getUserById($_SESSION['user_id']);
$customerDetails = $userModel->getCustomerDetails($_SESSION['user_id']); // Fetch customer details
var_dump($customerDetails);
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
    <header class="header">
      <h1>Welcome, <span id="customer-name"><?= htmlspecialchars($user['username']) ?></span>!</h1>
      <nav>
        <a href="#">Home</a>
        <a href="#">Shop</a>
        <a href="#">Logout</a>
      </nav>
    </header>

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

    <main class="main-content">
      <section class="personal-info">
        <h2>Personal Information</h2>
        <div class="info-card">
          <p><strong>Name:</strong> <span id="info-name"><?= htmlspecialchars($customerDetails['full_name']) ?></span></p>
          <p><strong>Username:</strong> <span id="info-username"><?= htmlspecialchars($user['username']) ?></span></p>
          <p><strong>Email:</strong> <span id="info-email"><?= htmlspecialchars($user['email']) ?></span></p>
          <p><strong>Phone:</strong> <span id="info-phone"><?= htmlspecialchars($customerDetails['phone']) ?></span></p>
          <p><strong>Billing Address:</strong> <span id="info-billing-address"><?= htmlspecialchars($customerDetails['billing_address']) ?></span></p>
          <p><strong>Shipping Address:</strong> <span id="info-shipping-address"><?= htmlspecialchars($customerDetails['shipping_address']) ?></span></p>
          <a href="update_profile.php" class="edit-btn">Edit Profile</a>
        </div>
      </section>

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
            <?php foreach ($orders as $order): ?>
              <tr>
                <td>#<?= $order['order_id'] ?></td>
                <td><?= $order['created_at'] ?></td>
                <td>$<?= $order['total_amount'] ?></td>
                <td><?= $order['shipping_status'] ?></td>
                <td>
                  <button class="action-btn" onclick="showOrderDetails(<?= $order['order_id'] ?>)">View Details</button>
                  <?php if ($order['shipping_status'] === 'Delivered'): ?>
                    <button class="return-btn" onclick="initiateReturn(<?= $order['order_id'] ?>)">Initiate Return</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="../scripts/profile.js"></script>
</body>
</html>
