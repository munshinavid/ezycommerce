<?php
/**
 * Single Entry Point (Front Controller)
 */

// Enable error reporting (disable or configure for true production environments)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the root directory of the application
define('BASE_PATH', dirname(__DIR__));

// Require the Global Error Handler
require_once BASE_PATH . '/utils/ErrorHandler.php';

/**
 * URL Helper Function
 * Automatically handles subdirectories if running without Docker (e.g. XAMPP)
 */
function url($path = '') {
    $path = ltrim($path, '/');
    // For local XAMPP testing, prefix with /ezycommerce. 
    // In Docker or Production (where DocumentRoot is public), prefix with /
    $basePath = (strpos($_SERVER['REQUEST_URI'], '/ezycommerce') === 0) ? '/ezycommerce' : '';
    // For root path, always return with trailing slash to ensure proper redirect
    if ($path === '') {
        return $basePath . '/';
    }
    return $basePath . '/' . $path;
}

// Parse the request URI (e.g., /admin?foo=bar -> /admin)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// For XAMPP: Remove the '/ezycommerce' sub-folder part from the URI
$subFolder = '/ezycommerce';
if (strpos($requestUri, $subFolder) === 0) {
    $requestUri = substr($requestUri, strlen($subFolder));
}

// Normalize URI by removing trailing slash if not root
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}
if (empty($requestUri)) {
    $requestUri = '/';
}

// Application Route Mapping
$routes = [
    // Customer Views
    '/' => '/Customer/views/index.php',
    '/cart' => '/Customer/views/cart.php',
    '/contact' => '/Customer/views/contact.php',
    '/debugging' => '/Customer/views/debugging.php',
    '/login' => '/Customer/views/login.php',
    '/logout' => '/Customer/views/logout.php',
    '/product' => '/Customer/views/product.php',
    '/profile' => '/Customer/views/profile.php',
    '/register' => '/Customer/views/register.php',
    '/wishlist' => '/Customer/views/wishlist.php',

    // Customer APIs / Controllers
    '/api/auth' => '/Customer/controllers/AuthController.php',
    '/api/logout' => '/Customer/controllers/AuthController.php',
    '/api/cart' => '/Customer/controllers/CartController.php',
    '/api/home' => '/Customer/controllers/HomeController.php',
    '/api/order' => '/Customer/controllers/OrderController.php',
    '/api/register_submit' => '/Customer/controllers/Reg_control.php',
    '/api/user' => '/Customer/controllers/UserController.php',
    '/api/user_update' => '/Customer/controllers/UserUpdate.php',

    // Admin Views
    '/admin' => '/Admin/views/index.php',
    '/admin/discounts' => '/Admin/views/discounts_management.php',
    '/admin/logout' => '/Customer/views/logout.php',
    '/admin/orders' => '/Admin/views/order_management.php',
    '/admin/products' => '/Admin/views/product_management.php',
    '/admin/users' => '/Admin/views/user_management.php',
    '/admin/reports' => '/Admin/reports.php',
    '/admin/settings' => '/Admin/settings.php',

    // Admin APIs
    '/api/admin/dashboard' => '/Admin/controllers/DashboardAPI.php',
    '/api/admin/dashboard_ctrl' => '/Admin/controllers/DashboardController.php',
    '/api/admin/discounts' => '/Admin/controllers/DiscountManageAPI.php',
    '/api/admin/order' => '/Admin/controllers/OrderController.php',
    '/api/admin/product' => '/Admin/controllers/ProductAPI.php',
    '/api/admin/user' => '/Admin/controllers/UserController.php',

    // Logistics Views
    '/logistics' => '/Logistics/views/dashboard.php',
    '/logistics/shipping' => '/Logistics/views/allshipping.php',
    '/logistics/returns' => '/Logistics/views/return.php',

    // Logistics APIs
    '/api/logistics/shipping' => '/Logistics/controllers/AllShippingAPI.php',
    '/api/logistics/dashboard' => '/Logistics/controllers/DashboardAPI.php',
    '/api/logistics/returns' => '/Logistics/controllers/ReturnAPI.php',

    // Logistics Logout
    '/logistics/logout' => '/Customer/views/logout.php',

    // Vendor Views
    '/vendor' => '/Vendor/views/dashboard.php',
    '/vendor/orders' => '/Vendor/views/orders.php',
    '/vendor/products' => '/Vendor/views/products.php',
    '/vendor/discounts' => '/Vendor/views/v-discount.php',
    '/vendor/logout' => '/Customer/views/logout.php',
    '/vendor/test' => '/Vendor/views/test.php',

    // Vendor APIs
    '/api/vendor/products' => '/Vendor/controllers/ProductsAPI.php',
    '/api/vendor/dashboard' => '/Vendor/controllers/vendor-Dashboard.php',
    '/api/vendor/discounts' => '/Vendor/controllers/vendor-DiscountAPI.php',
    '/api/vendor/orders' => '/Vendor/controllers/vendor-OrderAPI.php',
];

// API prefix matching (for sub-paths like /api/home/products)
$apiPrefixes = [
    // Customer APIs
    '/api/home'             => '/Customer/controllers/HomeController.php',
    '/api/cart'             => '/Customer/controllers/CartController.php',
    '/api/auth'             => '/Customer/controllers/AuthController.php',
    '/api/order'            => '/Customer/controllers/OrderController.php',
    '/api/user'             => '/Customer/controllers/UserController.php',
    '/api/user_update'      => '/Customer/controllers/UserUpdate.php',
    '/api/register_submit'  => '/Customer/controllers/Reg_control.php',
    // Admin APIs
    '/api/admin/dashboard'      => '/Admin/controllers/DashboardAPI.php',
    '/api/admin/dashboard_ctrl' => '/Admin/controllers/DashboardController.php',
    '/api/admin/discounts'      => '/Admin/controllers/DiscountManageAPI.php',
    '/api/admin/order'          => '/Admin/controllers/OrderController.php',
    '/api/admin/product'        => '/Admin/controllers/ProductAPI.php',
    '/api/admin/user'           => '/Admin/controllers/UserController.php',
    // Vendor APIs
    '/api/vendor/products'   => '/Vendor/controllers/ProductsAPI.php',
    '/api/vendor/dashboard'  => '/Vendor/controllers/vendor-Dashboard.php',
    '/api/vendor/discounts'  => '/Vendor/controllers/vendor-DiscountAPI.php',
    '/api/vendor/orders'     => '/Vendor/controllers/vendor-OrderAPI.php',
    // Logistics APIs
    '/api/logistics/shipping'  => '/Logistics/controllers/AllShippingAPI.php',
    '/api/logistics/dashboard' => '/Logistics/controllers/DashboardAPI.php',
    '/api/logistics/returns'   => '/Logistics/controllers/ReturnAPI.php',
];

// Sort prefixes by length descending to match more specific routes first
uksort($apiPrefixes, function($a, $b) {
    return strlen($b) - strlen($a);
});

foreach ($apiPrefixes as $prefix => $file) {
    if ($requestUri === $prefix || strpos($requestUri, $prefix . '/') === 0) {
        // Pass sub-path as PATH_INFO for controllers that use it
        $_SERVER['PATH_INFO'] = substr($requestUri, strlen($prefix)) ?: '/';
        $targetFile = BASE_PATH . $file;
        if (file_exists($targetFile)) {
            require_once $targetFile;
            exit;
        }
    }
}

// Exact route matching logic (views and pages)
if (array_key_exists($requestUri, $routes)) {
    $targetFile = BASE_PATH . $routes[$requestUri];
    if (file_exists($targetFile)) {
        require_once $targetFile;
    } else {
        http_response_code(404);
        echo "<h1>404 - File Not Found</h1>";
        echo "<p>The required file could not be found at: <em>" . htmlspecialchars($targetFile) . "</em></p>";
    }
} else {
    // Fallback logic for serving asset files from the root if requested dynamically 
    // (though .htaccess handles static assets, this prevents fatal errors if misconfigured)
    $assetPath = BASE_PATH . $requestUri;
    if (is_file($assetPath)) {
        $ext = pathinfo($assetPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
            readfile($assetPath);
            exit;
        }
    }

    // 404 Route Not Found
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested route '<strong>" . htmlspecialchars($requestUri) . "</strong>' was not found.</p>";
}
