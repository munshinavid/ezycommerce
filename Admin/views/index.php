<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header>
        <div class="logo">Ecomm Admin</div>
        <div class="user-profile">
            <img src="profile.jpg" alt="Profile">
            <span>Admin</span>
            <ul>
                <li><a href="#">Logout</a></li>
            </ul>
        </div>
    </header>

    <aside class="sidebar">
        <ul>
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="add_products.php" id="addProductLink"><i class="fas fa-box"></i> Add Product</a></li>
            <li><a href="manage_products.php" id="addProductLink"><i class="fas fa-box"></i> Manage Product</a></li>
            <li><a href="#"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#" id="userRequestsLink"><i class="fas fa-users"></i> User Requests</a></li>
            <li><a href="#"><i class="fas fa-list"></i> Categories</a></li>
        </ul>
    </aside>

    <main>
        <div class="dashboard-overview">
            <div class="card">
                <h3>Total Orders</h3>
                <p>150</p>
            </div>
            <div class="card">
                <h3>Total Revenue</h3>
                <p>$10,000</p>
            </div>
            <div class="card">
                <h3>Total Products</h3>
                <p>50</p>
            </div>
            <div class="card">
                <h3>Total Users</h3>
                <p>100</p>
            </div>
        </div>

        <div class="recent-orders">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>John Doe</td>
                        <td>2023-10-01</td>
                        <td>Shipped</td>
                        <td><a href="#">View Details</a></td>
                    </tr>
                    <!-- More rows -->
                </tbody>
            </table>
        </div>

        <!-- Add Product Modal -->
        

        <!-- User Requests Section -->
        
    </main>

    
</body>
</html>