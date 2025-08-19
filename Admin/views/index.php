
<?php echo "Hello World"; ?>
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
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Super Admin 👑</h2>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-users"></i>
                <span>Users Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-box"></i>
                <span>Products Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-tags"></i>
                <span>Discounts Management</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports & Analytics</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </div>
            <div class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </div>
        </div>
    </div>

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
                    <h3>1,254</h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>3,587</h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>2,143</h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>$45,289</h3>
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
                    <tr>
                        <td>#ORD-7842</td>
                        <td>John Smith</td>
                        <td>Jun 24, 2023</td>
                        <td>$128.99</td>
                        <td><span class="status status-delivered">Delivered</span></td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>#ORD-7841</td>
                        <td>Emma Johnson</td>
                        <td>Jun 24, 2023</td>
                        <td>$89.50</td>
                        <td><span class="status status-shipped">Shipped</span></td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>#ORD-7840</td>
                        <td>Michael Brown</td>
                        <td>Jun 23, 2023</td>
                        <td>$245.75</td>
                        <td><span class="status status-pending">Pending</span></td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>#ORD-7839</td>
                        <td>Sarah Wilson</td>
                        <td>Jun 23, 2023</td>
                        <td>$54.25</td>
                        <td><span class="status status-cancelled">Cancelled</span></td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>#ORD-7838</td>
                        <td>David Miller</td>
                        <td>Jun 22, 2023</td>
                        <td>$189.99</td>
                        <td><span class="status status-delivered">Delivered</span></td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
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
                    <tr>
                        <td>Wireless Headphones</td>
                        <td>Electronics</td>
                        <td>$129.99</td>
                        <td>345</td>
                        <td>56</td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>Running Shoes</td>
                        <td>Sports</td>
                        <td>$89.99</td>
                        <td>278</td>
                        <td>42</td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>Smart Watch</td>
                        <td>Electronics</td>
                        <td>$199.99</td>
                        <td>213</td>
                        <td>12</td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>Cotton T-Shirt</td>
                        <td>Fashion</td>
                        <td>$24.99</td>
                        <td>187</td>
                        <td>98</td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                    <tr>
                        <td>Coffee Maker</td>
                        <td>Home & Kitchen</td>
                        <td>$79.99</td>
                        <td>156</td>
                        <td>23</td>
                        <td><i class="fas fa-edit"></i></td>
                    </tr>
                </tbody>
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