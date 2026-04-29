<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Capture the user's role before clearing the session
$userRole = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(
		session_name(),
		'',
		time() - 42000,
		$params['path'],
		$params['domain'],
		$params['secure'],
		$params['httponly']
	);
}

session_destroy();

// Determine redirect based on user role first, then referer as fallback
if ($userRole === 'admin' || strpos($referer, '/admin') !== false) {
    header('Location: /ezycommerce/login');
} elseif ($userRole === 'logistics' || strpos($referer, '/logistics') !== false) {
    header('Location: /ezycommerce/login');
} elseif ($userRole === 'vendor' || strpos($referer, '/vendor') !== false) {
    header('Location: /ezycommerce/login');
} else {
    // Customer logout - redirect to login page
    header('Location: /ezycommerce/login');
}
exit;
