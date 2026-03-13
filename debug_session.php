<?php
session_start();

echo "<h2>Session Debug Information</h2>";

echo "<h3>Current Session Data:</h3>";
if (empty($_SESSION)) {
    echo "<p style='color: red;'>No session data found</p>";
} else {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}

echo "<h3>Session Configuration:</h3>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";

echo "<h3>Test Login:</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        require_once 'config/database.php';
        $conn = db_get_connection();
        
        $query = "SELECT id, username, password_hash, full_name, role, is_active FROM users WHERE username = :username LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<p>User found: " . htmlspecialchars($row['username']) . "</p>";
            echo "<p>Password verification: " . (password_verify($password, $row['password_hash']) ? "SUCCESS" : "FAILED") . "</p>";
            echo "<p>User active: " . ($row['is_active'] ? "YES" : "NO") . "</p>";
            
            if (password_verify($password, $row['password_hash']) && $row['is_active']) {
                // Set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role'] = $row['role'];
                
                echo "<p style='color: green;'>Session variables set successfully!</p>";
                echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
            }
        } else {
            echo "<p style='color: red;'>User not found</p>";
        }
    }
}

echo "<h3>Test Form:</h3>";
?>
<form method="post">
    <p>
        Username: <input type="text" name="username" value="admin">
    </p>
    <p>
        Password: <input type="password" name="password" value="admin123">
    </p>
    <p>
        <button type="submit">Test Login</button>
    </p>
</form>

<p><a href="login.php">Go to Login Page</a></p>
<p><a href="dashboard.php">Go to Dashboard</a></p>
