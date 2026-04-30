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

// Redirect to login page
header('Location: ' . url('/login'));
exit;
