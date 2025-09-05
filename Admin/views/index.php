
<?php
require_once __DIR__ . '/../controllers/DashboardController.php';
$dashboard = new DashboardController();

$stats = $dashboard->getStats();
$recentOrders = $dashboard->getRecentOrders();
$topProducts = $dashboard->getTopProducts();
?>


<!DOCTYPE html>
<!-- ...existing code... -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Super Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include '../layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=Super+Admin&background=random" alt="Admin">
                <div>
                    <h4>Super Admin</h4>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
                <div class="dashboard-cards">
            <div class="card stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_users']; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_products']; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>


        <!-- Charts -->
        <div class="charts-container">
            <div class="chart">
                <div class="chart-header">
                    <h3>Sales Overview</h3>
                    <select class="btn">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                        <option>Last 90 Days</option>
                    </select>
                </div>
                <div class="chart-placeholder">
                    Sales Chart (to be implemented with Chart.js)
                </div>
            </div>
            <div class="chart">
                <div class="chart-header">
                    <h3>Revenue by Category</h3>
                </div>
                <div class="chart-placeholder">
                    Pie Chart (to be implemented with Chart.js)
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="table-container">
            <div class="table-header">
                <h3>Recent Orders</h3>
                <button class="btn btn-primary">View All</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>#ORD-<?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['customer']; ?></td>
                        <td><?php echo date("M d, Y", strtotime($order['created_at'])); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="status status-<?php echo strtolower($order['order_status']); ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <!-- Top Products -->
        <div class="table-container">
            <div class="table-header">
                <h3>Top Selling Products</h3>
                <button class="btn btn-primary">View All</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Sold</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $product): ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['sold']; ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // Simple JavaScript for menu interaction
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(i => {
                    i.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>