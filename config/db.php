<?php
/**
 * Central Database Configuration
 * 
 * ALL Database classes must include this file.
 * Reads from environment variables (.env via Docker) with local fallbacks.
 * 
 * DO NOT hardcode credentials anywhere else in the project.
 */

$DB_CONFIG = [
    'host'     => getenv('DB_HOST')     ?: 'localhost',
    'port'     => (int)(getenv('DB_PORT') ?: 3306),
    'database' => getenv('DB_NAME')     ?: 'ecomm',
    'username' => getenv('DB_USER')     ?: 'root',
    'password' => getenv('DB_PASS')     ?: '',
    'charset'  => 'utf8mb4',
];

return $DB_CONFIG;
