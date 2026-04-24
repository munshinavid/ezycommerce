<?php
require 'Customer/models/db.php';
$db = new Database();
$user = $db->select("SELECT u.user_id, u.username, u.email, u.password, r.role_name FROM Users u JOIN Roles r ON u.role_id = r.role_id WHERE u.email = ?", ['customer@test.com']);
var_dump($user);
if (!empty($user)) {
    var_dump(password_verify('password', $user[0]['password']));
}
