<?php
// Register
$ch = curl_init('http://localhost/ezycommerce/Customer/controllers/AuthController.php?endpoint=register');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'username' => 'test_user_plain',
    'firstName' => 'Test',
    'lastName' => 'User',
    'email' => 'plain@test.com',
    'phone' => '1234567890',
    'password' => 'PlainPass123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
var_dump("Register:", $httpcode, $response);

// Login
$ch = curl_init('http://localhost/ezycommerce/Customer/controllers/AuthController.php?endpoint=login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'plain@test.com',
    'password' => 'PlainPass123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
var_dump("Login:", $httpcode, $response);
