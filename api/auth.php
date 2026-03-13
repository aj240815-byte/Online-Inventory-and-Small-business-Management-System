<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

class Auth {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    public function login($username, $password) {
        // Query to get user by username
        $query = "SELECT id, username, password, full_name, role, is_active FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password (for demo, using plain text comparison - in production, use password_verify)
            if ($password === $row['password'] || password_verify($password, $row['password'])) {
                // Check if user is active
                if (!$row['is_active']) {
                    return ['success' => false, 'message' => 'Account is deactivated'];
                }
                
                // Update last login
                $update_query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(':id', $row['id']);
                $update_stmt->execute();
                
                // Generate simple token (in production, use JWT)
                $token = base64_encode($row['id'] . ':' . time() . ':' . md5($row['username']));
                
                // Remove password from response
                unset($row['password']);
                
                return [
                    'success' => true,
                    'user' => $row,
                    'token' => $token
                ];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    public function logout($token) {
        // In a real implementation, you would invalidate the token
        // For now, we'll just return success
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    public function verifyToken($token) {
        // Simple token verification (in production, use JWT verification)
        $decoded = base64_decode($token);
        if ($decoded) {
            $parts = explode(':', $decoded);
            if (count($parts) === 3) {
                $user_id = $parts[0];
                $timestamp = $parts[1];
                
                // Check if token is not too old (24 hours)
                if (time() - $timestamp < 86400) {
                    $query = "SELECT id, username, full_name, role, is_active FROM users WHERE id = :id AND is_active = 1 LIMIT 1";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $user_id);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        return ['success' => true, 'user' => $stmt->fetch(PDO::FETCH_ASSOC)];
                    }
                }
            }
        }
        
        return ['success' => false, 'message' => 'Invalid or expired token'];
    }
}

// Initialize database and auth
$database = new Database();
$auth = new Auth($database);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$action = $input['action'] ?? '';

switch ($action) {
    case 'login':
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username and password are required']);
            exit;
        }
        
        $result = $auth->login($username, $password);
        echo json_encode($result);
        break;
        
    case 'logout':
        $token = $input['token'] ?? '';
        $result = $auth->logout($token);
        echo json_encode($result);
        break;
        
    case 'verify':
        $token = $input['token'] ?? '';
        $result = $auth->verifyToken($token);
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
