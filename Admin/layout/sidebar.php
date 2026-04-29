<!-- sidebar.php -->
<?php
// Determine current page for active state
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = str_replace('/ezycommerce', '', $requestUri);
$currentPage = trim($requestUri, '/');

// Helper function to check if link is active
function isActive($page) {
    global $currentPage;
    return strpos($currentPage, trim($page, '/')) === 0 ? 'active' : '';
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>Super Admin 👑</h2>
    </div>
    <div class="sidebar-menu">
                <a href="<?php echo url('/admin'); ?>" 
        class="menu-item <?php echo isActive('admin'); ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo url('/admin/users'); ?>" 
        class="menu-item <?php echo isActive('admin/users'); ?>">
            <i class="fas fa-users"></i>
            <span>Users Management</span>
        </a>

        <a href="<?php echo url('/admin/products'); ?>" 
        class="menu-item <?php echo isActive('admin/products'); ?>">
            <i class="fas fa-box"></i>
            <span>Products Management</span>
        </a>

        <a href="<?php echo url('/admin/orders'); ?>" 
        class="menu-item <?php echo isActive('admin/orders'); ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders Management</span>
        </a>

        <a href="<?php echo url('/admin/discounts'); ?>" 
        class="menu-item <?php echo isActive('admin/discounts'); ?>">
            <i class="fas fa-tags"></i>
            <span>Discounts Management</span>
        </a>

        <a href="<?php echo url('/admin/reports'); ?>" 
        class="menu-item <?php echo isActive('admin/reports'); ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports & Analytics</span>
        </a>

        <a href="<?php echo url('/admin/settings'); ?>" 
        class="menu-item <?php echo isActive('admin/settings'); ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>

        <a href="<?php echo url('/admin/logout'); ?>" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>

    </div>
</div>
