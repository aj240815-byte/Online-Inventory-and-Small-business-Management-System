<?php
/**
 * JIMS Installation and Setup Script
 * Jewellery Inventory Management System
 */

// Error reporting for setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Prevent direct access after installation
if (file_exists('config/installed.lock')) {
    die('JIMS is already installed. To reinstall, delete the config/installed.lock file.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JIMS - Installation Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 90%;
            margin: 20px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .content {
            padding: 2rem;
        }
        
        .step {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f9f9f9;
        }
        
        .step-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .step-number {
            background: #3498db;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .step-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .btn {
            background: #3498db;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .requirements {
            list-style: none;
        }
        
        .requirements li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .requirements li:last-child {
            border-bottom: none;
        }
        
        .check {
            color: #27ae60;
            margin-right: 0.5rem;
        }
        
        .cross {
            color: #e74c3c;
            margin-right: 0.5rem;
        }
        
        .progress {
            width: 100%;
            height: 20px;
            background: #ecf0f1;
            border-radius: 10px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            transition: width 0.3s ease;
        }
        
        .hidden {
            display: none;
        }
        
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-gem"></i> JIMS</h1>
            <p>Jewellery Inventory Management System - Installation</p>
        </div>
        
        <div class="content">
            <?php
            $step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
            
            switch ($step) {
                case 1:
                    showStep1();
                    break;
                case 2:
                    showStep2();
                    break;
                case 3:
                    showStep3();
                    break;
                case 4:
                    showStep4();
                    break;
                default:
                    showStep1();
                    break;
            }
            ?>
        </div>
    </div>
    
    <?php
    function showStep1() {
        ?>
        <form method="post" id="setupForm">
            <input type="hidden" name="step" value="2">
            
            <div class="step">
                <div class="step-header">
                    <div class="step-number">1</div>
                    <div class="step-title">System Requirements Check</div>
                </div>
                
                <ul class="requirements">
                    <?php
                    $requirements = checkRequirements();
                    foreach ($requirements as $req) {
                        echo '<li>' . ($req['status'] ? '<span class="check">✓</span>' : '<span class="cross">✗</span>') . ' ' . $req['message'] . '</li>';
                    }
                    ?>
                </ul>
                
                <?php if (!allRequirementsMet($requirements)) { ?>
                    <div class="alert alert-warning">
                        Please fix the above requirements before proceeding with installation.
                    </div>
                    <button type="submit" class="btn" disabled>Next Step</button>
                <?php } else { ?>
                    <div class="alert alert-success">
                        All system requirements are met. You can proceed with installation.
                    </div>
                    <button type="submit" class="btn">Next Step</button>
                <?php } ?>
            </div>
        </form>
        <?php
    }
    
    function showStep2() {
        ?>
        <form method="post" id="setupForm">
            <input type="hidden" name="step" value="3">
            
            <div class="step">
                <div class="step-header">
                    <div class="step-number">2</div>
                    <div class="step-title">Database Configuration</div>
                </div>
                
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" value="jims_db" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="db_user">Database Username</label>
                        <input type="text" id="db_user" name="db_user" value="root" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_password">Database Password</label>
                        <input type="password" id="db_password" name="db_password">
                    </div>
                </div>
                
                <button type="submit" class="btn">Test Connection & Continue</button>
            </div>
        </form>
        <?php
    }
    
    function showStep3() {
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_name = $_POST['db_name'] ?? 'jims_db';
        $db_user = $_POST['db_user'] ?? 'root';
        $db_password = $_POST['db_password'] ?? '';
        
        // Test database connection
        $connection_test = testDatabaseConnection($db_host, $db_name, $db_user, $db_password);
        
        if (!$connection_test['success']) {
            ?>
            <div class="alert alert-error">
                Database connection failed: <?php echo $connection_test['error']; ?>
            </div>
            <form method="post" id="setupForm">
                <input type="hidden" name="step" value="2">
                <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>">
                <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>">
                <input type="hidden" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>">
                <button type="submit" class="btn">Go Back</button>
            </form>
            <?php
            return;
        }
        
        // Install database
        $install_result = installDatabase($db_host, $db_name, $db_user, $db_password);
        
        if (!$install_result['success']) {
            ?>
            <div class="alert alert-error">
                Database installation failed: <?php echo $install_result['error']; ?>
            </div>
            <form method="post" id="setupForm">
                <input type="hidden" name="step" value="2">
                <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>">
                <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>">
                <input type="hidden" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>">
                <button type="submit" class="btn">Go Back</button>
            </form>
            <?php
            return;
        }
        
        // Update configuration file
        $config_result = updateConfigFile($db_host, $db_name, $db_user, $db_password);
        
        ?>
        <form method="post" id="setupForm">
            <input type="hidden" name="step" value="4">
            <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>">
            <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>">
            <input type="hidden" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>">
            <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($db_password); ?>">
            
            <div class="step">
                <div class="step-header">
                    <div class="step-number">3</div>
                    <div class="step-title">Database Installation Complete</div>
                </div>
                
                <div class="alert alert-success">
                    ✓ Database connection successful<br>
                    ✓ Database schema installed<br>
                    ✓ Configuration file updated
                </div>
                
                <button type="submit" class="btn">Next Step</button>
            </div>
        </form>
        <?php
    }
    
    function showStep4() {
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_name = $_POST['db_name'] ?? 'jims_db';
        $db_user = $_POST['db_user'] ?? 'root';
        $db_password = $_POST['db_password'] ?? '';
        
        // Create installation lock file
        createLockFile();
        
        ?>
        <div class="step">
            <div class="step-header">
                <div class="step-number">4</div>
                <div class="step-title">Installation Complete!</div>
            </div>
            
            <div class="alert alert-success">
                <h3>🎉 JIMS has been successfully installed!</h3>
                <p>Your Jewellery Inventory Management System is ready to use.</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin: 1rem 0;">
                <h4>Default Login Credentials:</h4>
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> admin123</p>
                <p class="alert-warning" style="margin-top: 1rem;">
                    <strong>Important:</strong> Please change the default password after your first login.
                </p>
            </div>
            
            <div style="background: #e8f5e8; padding: 1rem; border-radius: 5px; margin: 1rem 0;">
                <h4>Next Steps:</h4>
                <ol style="margin-left: 1.5rem;">
                    <li>Delete the setup.php file for security</li>
                    <li>Log in with the default credentials</li>
                    <li>Change the admin password</li>
                    <li>Configure your store settings</li>
                    <li>Add your products and suppliers</li>
                </ol>
            </div>
            
            <div class="text-center">
                <a href="index.html" class="btn btn-success">Go to JIMS Dashboard</a>
            </div>
        </div>
        <?php
    }
    
    function checkRequirements() {
        $requirements = [];
        
        // PHP Version
        $php_version = PHP_VERSION;
        $requirements[] = [
            'status' => version_compare($php_version, '7.4.0', '>='),
            'message' => "PHP Version: $php_version (7.4.0 or higher required)"
        ];
        
        // Required PHP Extensions
        $required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'curl'];
        foreach ($required_extensions as $ext) {
            $requirements[] = [
                'status' => extension_loaded($ext),
                'message' => "PHP Extension: $ext " . (extension_loaded($ext) ? '(Installed)' : '(Missing)')
            ];
        }
        
        // File Permissions
        $writable_dirs = ['config/', 'uploads/'];
        foreach ($writable_dirs as $dir) {
            $writable = is_writable($dir);
            $requirements[] = [
                'status' => $writable,
                'message' => "Directory Writable: $dir " . ($writable ? '(Writable)' : '(Not writable)')
            ];
        }
        
        return $requirements;
    }
    
    function allRequirementsMet($requirements) {
        foreach ($requirements as $req) {
            if (!$req['status']) {
                return false;
            }
        }
        return true;
    }
    
    function testDatabaseConnection($host, $name, $user, $password) {
        try {
            $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    function installDatabase($host, $name, $user, $password) {
        try {
            $pdo = new PDO("mysql:host=$host", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Use the database
            $pdo->exec("USE `$name`");
            
            // Read and execute schema file
            $schema_file = __DIR__ . '/database/schema.sql';
            if (!file_exists($schema_file)) {
                return ['success' => false, 'error' => 'Database schema file not found'];
            }
            
            $schema = file_get_contents($schema_file);
            $statements = array_filter(array_map('trim', explode(';', $schema)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    function updateConfigFile($host, $name, $user, $password) {
        $config_file = __DIR__ . '/config/database.php';
        
        if (!file_exists($config_file)) {
            return ['success' => false, 'error' => 'Config file not found'];
        }
        
        $config_content = file_get_contents($config_file);
        
        // Update database configuration
        $config_content = str_replace("private \$host = 'localhost';", "private \$host = '$host';", $config_content);
        $config_content = str_replace("private \$db_name = 'jims_db';", "private \$db_name = '$name';", $config_content);
        $config_content = str_replace("private \$username = 'root';", "private \$username = '$user';", $config_content);
        $config_content = str_replace("private \$password = '';", "private \$password = '$password';", $config_content);
        
        if (file_put_contents($config_file, $config_content)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to update config file'];
        }
    }
    
    function createLockFile() {
        $lock_file = __DIR__ . '/config/installed.lock';
        file_put_contents($lock_file, date('Y-m-d H:i:s'));
    }
    ?>
    
    <script>
        // Auto-submit form on Enter key
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const form = document.getElementById('setupForm');
                if (form && document.activeElement.tagName !== 'TEXTAREA') {
                    form.submit();
                }
            }
        });
    </script>
</body>
</html>
