<!-- sidebar.php -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>Super Admin 👑</h2>
    </div>
    <div class="sidebar-menu">
                <a href="<?php echo url('/admin'); ?>" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo url('/admin/users'); ?>" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='user_management.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Users Management</span>
        </a>

        <a href="<?php echo url('/admin/products'); ?>" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='product_management.php' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i>
            <span>Products Management</span>
        </a>

        <a href="<?php echo url('/admin/orders'); ?>" 
        class="menu-item <?php echo basename($_SERVER['PHP_SELF'])=='order_management.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders Management</span>
        </a>

        <a href="<?php echo url('/admin/discounts'); ?>" 
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

        <a href="<?php echo url('/admin/logout'); ?>" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>

    </div>
</div>
