<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
session_start();

// Check authentication
if (!isset($_SESSION['delivery_man_id']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Include Order model
require_once '../db/model.php';
$orderModel = new Order();

// Get orders with error handling
try {
    $orders = $orderModel->getOrdersByDeliveryMan($_SESSION['delivery_man_id']);
} catch (Exception $e) {
    $error = "Error fetching orders: " . $e->getMessage();
    $orders = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delivery Dashboard</title>
    
    <link rel="stylesheet" href="../CSS/style.css">
   
</head>
<body>
    <h1>Welcome, <?php echo isset($_SESSION['delivery_man_name']) ? htmlspecialchars($_SESSION['delivery_man_name']) : 'Delivery Partner'; ?></h1>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <h2>Your Orders</h2>
    
    <?php if (!empty($orders)): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>">
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['delivery_address']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_details']); ?></td>
                    <td>
                        <select class="status-select">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        </select>
                    </td>
                    <td>
                        <button class="update-btn">Update</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders assigned yet.</p>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
    <div class="empty-state">
        <p>No orders assigned yet.</p>
    </div>
<?php endif; ?>

    <script src="../JS/dashboard.js"></script>
</body>
</html>