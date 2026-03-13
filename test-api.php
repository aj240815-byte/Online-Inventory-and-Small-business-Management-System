<?php
/**
 * API Test Script for JIMS
 * Use this to test API endpoints and debug connection issues
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Test database connection
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$response = [
    'status' => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'database_connection' => $db ? 'success' : 'failed',
    'server_info' => [
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
    ]
];

if ($db) {
    try {
        // Test basic query
        $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['database_test'] = [
            'categories_count' => $result['count'],
            'status' => 'success'
        ];
    } catch (Exception $e) {
        $response['database_test'] = [
            'status' => 'error',
            'error' => $e->getMessage()
        ];
    }
}

// Test if required files exist
$response['files'] = [
    'database_schema' => file_exists('database/schema.sql'),
    'database_config' => file_exists('config/database.php'),
    'products_api' => file_exists('api/products.php'),
    'sales_api' => file_exists('api/sales.php'),
    'suppliers_api' => file_exists('api/suppliers.php'),
    'reports_api' => file_exists('api/reports.php'),
    'main_js' => file_exists('assets/js/app.js'),
    'main_css' => file_exists('assets/css/style.css'),
    'index_html' => file_exists('index.html')
];

// Check PHP extensions
$response['php_extensions'] = [
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'json' => extension_loaded('json'),
    'mbstring' => extension_loaded('mbstring'),
    'curl' => extension_loaded('curl')
];

// Current working directory and permissions
$response['file_system'] = [
    'current_dir' => getcwd(),
    'config_writable' => is_writable('config/'),
    'uploads_writable' => is_dir('uploads/') ? is_writable('uploads/') : 'uploads_dir_missing'
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
