<?php
// scripts/test_verify.php
// Usage: php scripts/test_verify.php email password
require_once __DIR__ . '/../Customer/models/db.php';

$email = $argv[1] ?? null;
$password = $argv[2] ?? null;
if (!$email || !$password) {
    echo "Usage: php scripts/test_verify.php email password\n";
    exit(1);
}
$db = new Database();
$u = $db->select('SELECT user_id,email,password FROM users WHERE email = ?', [$email]);
if (empty($u)) {
    echo "NO_USER\n";
    exit(0);
}
$h = $u[0]['password'];
echo "EMAIL: {$u[0]['email']}\n";
echo "HASH_LEN: " . strlen($h) . "\n";
echo "HASH_HEAD: " . substr($h,0,60) . "...\n";
echo password_verify($password, $h) ? "VERIFY_OK\n" : "VERIFY_FAIL\n";
