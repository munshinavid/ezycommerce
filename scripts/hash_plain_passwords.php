<?php
// scripts/hash_plain_passwords.php
// Run: php scripts/hash_plain_passwords.php

require_once __DIR__ . '/../Customer/models/db.php';

echo "Starting password audit and re-hash...\n";
$db = new Database();

$users = $db->select("SELECT user_id, email, password FROM users");

$updated = 0;
foreach ($users as $u) {
    $userId = $u['user_id'];
    $email = $u['email'];
    $pwd = $u['password'];

    // Detect common PHP password_hash outputs (bcrypt / argon2 / others)
    if (preg_match('/^\$2y\$|^\$2a\$|^\$argon2i\$|^\$argon2id\$|^\$pbkdf2\$/i', $pwd)) {
        // already hashed
        continue;
    }

    // If the password field looks like a plain password (shorter than 60 or not matching hash patterns), re-hash it
    $newHash = password_hash($pwd, PASSWORD_DEFAULT);
    if ($newHash) {
        $db->update("UPDATE users SET password = ? WHERE user_id = ?", [$newHash, $userId]);
        echo "Re-hashed password for user_id={$userId} ({$email})\n";
        $updated++;
    }
}

echo "Done. Total updated: {$updated}\n";
