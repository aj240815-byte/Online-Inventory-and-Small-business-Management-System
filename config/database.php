<?php
// Very simple database helper for school project (no classes)

function db_get_connection() {
    $host = 'localhost';
    $db_name = 'jims_db';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    try {
        $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        return null;
    }
}

// Simple configuration values as global array
$APP_CONFIG = [
    'app_name' => 'JIMS - Jewellery Inventory Management System',
    'app_version' => '1.0.0',
    'timezone' => 'UTC',
    'date_format' => 'Y-m-d',
    'datetime_format' => 'Y-m-d H:i:s',
    'password_min_length' => 8,
    'session_timeout' => 3600,
    'default_page_size' => 20,
    'currency' => 'USD',
    'currency_symbol' => '$',
    'default_tax_rate' => 0.00,
];
?>
