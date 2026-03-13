<?php
/**
 * Database Configuration for JIMS
 * Jewellery Inventory Management System
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'jims_db';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            return null;
        }
        
        return $this->conn;
    }
    
    // Test database connection
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                return true;
            }
        } catch(PDOException $e) {
            return false;
        }
        return false;
    }
}

// System configuration
class Config {
    // Database settings
    const DB_HOST = 'localhost';
    const DB_NAME = 'jims_db';
    const DB_USER = 'root';
    const DB_PASS = '';
    
    // Application settings
    const APP_NAME = 'JIMS - Jewellery Inventory Management System';
    const APP_VERSION = '1.0.0';
    const TIMEZONE = 'UTC';
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    
    // Security settings
    const PASSWORD_MIN_LENGTH = 8;
    const SESSION_TIMEOUT = 3600; // 1 hour
    
    // Pagination
    const DEFAULT_PAGE_SIZE = 20;
    
    // File upload settings
    const MAX_FILE_SIZE = 5242880; // 5MB
    const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    // Currency settings
    const CURRENCY = 'USD';
    const CURRENCY_SYMBOL = '$';
    
    // Tax settings
    const DEFAULT_TAX_RATE = 0.00; // Can be overridden per sale
    
    // Email settings (configure as needed)
    const SMTP_HOST = '';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = '';
    const SMTP_PASSWORD = '';
    const SMTP_ENCRYPTION = 'tls';
    
    // Backup settings
    const BACKUP_PATH = 'backups/';
    const AUTO_BACKUP_ENABLED = true;
    const BACKUP_INTERVAL = 'daily'; // daily, weekly, monthly
}
?>
