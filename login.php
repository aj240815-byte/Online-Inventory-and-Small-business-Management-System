<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        require_once 'config/database.php';
        
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT id, username, password_hash, full_name, role, is_active FROM users WHERE username = :username LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($password, $row['password_hash'])) {
                error_log("Password verified successfully for user: " . $username);
                if ($row['is_active']) {
                    error_log("User is active, setting session variables");
                    // Set session variables
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['full_name'] = $row['full_name'];
                    $_SESSION['role'] = $row['role'];
                    
                    error_log("Session variables set: " . print_r($_SESSION, true));
                    
                    // Update last login
                    $update_query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bindParam(':id', $row['id']);
                    $update_stmt->execute();
                    
                    error_log("Redirecting to dashboard.php");
                    header('Location: dashboard.php');
                    exit();
                } else {
                    error_log("User account is deactivated");
                    $error = "Account is deactivated";
                }
            } else {
                error_log("Password verification failed for user: " . $username);
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Please enter username and password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JIMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Disable global gradient overlay on this page */
        body::before {
            display: none !important;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .login-header {
            background-color: #111827;
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .login-header .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .login-header h2 {
            margin: 0 0 10px 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #111827;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.25);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .default-credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 25px;
            font-size: 0.85rem;
            border-left: 4px solid #d1d5db;
        }
        
        .default-credentials strong {
            color: #333;
            display: block;
            margin-bottom: 8px;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        @media (max-width: 480px) {
            .login-wrapper {
                padding: 10px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-gem"></i>
                    <span>JIMS</span>
                </div>
                <h2>Jewellery Inventory Management</h2>
                <p>Please sign in to continue</p>
            </div>
            
            <div class="login-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               placeholder="Enter your username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
                
                <div class="default-credentials">
                    <strong>Default Credentials:</strong>
                    Username: admin<br>
                    Password: admin123
                </div>
            </div>
        </div>
        
        <div class="login-footer">
            <p>&copy; 2026 JIMS - Jewellery Inventory Management System</p>
        </div>
    </div>
</body>
</html>
