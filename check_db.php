<?php
/**
 * Database Check and Setup Script
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>JIMS Database Check</h2>";

try {
    // Check if database config exists
    if (!file_exists('config/database.php')) {
        echo "<p style='color: red;'>Database config file not found. Please run setup.php first.</p>";
        exit;
    }
    
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if users table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: orange;'>⚠ Users table not found. Creating database schema...</p>";
        
        // Read and execute schema
        $schema = file_get_contents('database/schema.sql');
        if ($schema) {
            // Split schema into individual statements
            $statements = array_filter(array_map('trim', explode(';', $schema)));
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (PDOException $e) {
                        echo "<p style='color: orange;'>Warning: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                }
            }
            echo "<p style='color: green;'>✓ Database schema created</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Users table exists</p>";
    }
    
    // Check if admin user exists
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: orange;'>⚠ Admin user not found. Creating admin user...</p>";
        
        // Create admin user with hashed password
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@jims.com', $password_hash, 'System Administrator', 'admin']);
        
        echo "<p style='color: green;'>✓ Admin user created (username: admin, password: admin123)</p>";
    } else {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p style='color: green;'>✓ Admin user exists (username: admin)</p>";
        
        // Test password verification
        if (password_verify('admin123', $user['password_hash'])) {
            echo "<p style='color: green;'>✓ Admin password verification works</p>";
        } else {
            echo "<p style='color: red;'>✗ Admin password verification failed. Resetting password...</p>";
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
            $stmt->execute([$new_hash]);
            echo "<p style='color: green;'>✓ Admin password reset to: admin123</p>";
        }
    }
    
    echo "<h3>Database Status: Ready</h3>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}
?>
