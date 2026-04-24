<!-- sidebar.php -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>Super Admin 👑</h2>
    </div>
    <div class="sidebar-menu">
                <a href="../views/index.php" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="../views/user_management.php" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='user_management.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Users Management</span>
        </a>

        <a href="../views/product_management.php" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='product_management.php' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i>
            <span>Products Management</span>
        </a>

        <a href="../views/order_management.php" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='order_management.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders Management</span>
        </a>

        <a href="../views/discounts_management.php" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='discounts_management.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i>
            <span>Discounts Management</span>
        </a>

        <a href="reports.php" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports & Analytics</span>
        </a>

        <a href="settings.php" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>

        <a href="/ezycommerce/Admin/logout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>

    </div>
</div>
