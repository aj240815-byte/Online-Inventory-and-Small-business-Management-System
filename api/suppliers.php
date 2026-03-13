<?php
/**
 * Suppliers API for JIMS
 * Handles supplier management operations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Check database connection
if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getSupplier($db, $_GET['id']);
        } else {
            getSuppliers($db);
        }
        break;
    case 'POST':
        createSupplier($db);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            updateSupplier($db, $_GET['id']);
        } else {
            echo json_encode(['error' => 'Supplier ID required']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteSupplier($db, $_GET['id']);
        } else {
            echo json_encode(['error' => 'Supplier ID required']);
        }
        break;
    case 'OPTIONS':
        http_response_code(200);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function getSuppliers($db) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $is_active = isset($_GET['is_active']) ? (bool)$_GET['is_active'] : null;
    
    // Build WHERE clause
    $where = [];
    $params = [];
    
    if (!empty($search)) {
        $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR contact_person LIKE ?)';
        $search_param = "%$search%";
        array_push($params, $search_param, $search_param, $search_param, $search_param);
    }
    
    if ($is_active !== null) {
        $where[] = 'is_active = ?';
        $params[] = $is_active;
    }
    
    $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM suppliers $where_clause";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get suppliers
    $query = "SELECT s.*, 
              (SELECT COUNT(*) FROM products WHERE supplier_id = s.id AND is_active = 1) as product_count,
              (SELECT COALESCE(SUM(po.total_amount), 0) FROM purchase_orders po WHERE po.supplier_id = s.id AND po.status != 'cancelled') as total_purchase_value
              FROM suppliers s 
              $where_clause 
              ORDER BY s.name ASC 
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'suppliers' => $suppliers,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

function getSupplier($db, $id) {
    $query = "SELECT s.* FROM suppliers s WHERE s.id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($supplier) {
        // Get supplier's products
        $products_query = "SELECT id, name, sku, cost_price, selling_price, stock_quantity 
                          FROM products 
                          WHERE supplier_id = ? AND is_active = 1 
                          ORDER BY name ASC";
        
        $products_stmt = $db->prepare($products_query);
        $products_stmt->execute([$id]);
        $products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get purchase orders
        $po_query = "SELECT id, po_number, order_date, expected_delivery_date, status, total_amount 
                    FROM purchase_orders 
                    WHERE supplier_id = ? 
                    ORDER BY order_date DESC 
                    LIMIT 10";
        
        $po_stmt = $db->prepare($po_query);
        $po_stmt->execute([$id]);
        $purchase_orders = $po_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $supplier['products'] = $products;
        $supplier['recent_purchase_orders'] = $purchase_orders;
        
        echo json_encode($supplier);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Supplier not found']);
    }
}

function createSupplier($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Validate required fields
    if (empty($data['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Supplier name is required']);
        return;
    }
    
    // Check if email already exists
    if (!empty($data['email'])) {
        $check_query = "SELECT id FROM suppliers WHERE email = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$data['email']]);
        if ($check_stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }
    }
    
    $query = "INSERT INTO suppliers (name, contact_person, email, phone, address, city, state, 
              postal_code, country, tax_id, payment_terms, notes, is_active) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $data['name'],
        $data['contact_person'] ?? '',
        $data['email'] ?? '',
        $data['phone'] ?? '',
        $data['address'] ?? '',
        $data['city'] ?? '',
        $data['state'] ?? '',
        $data['postal_code'] ?? '',
        $data['country'] ?? '',
        $data['tax_id'] ?? '',
        $data['payment_terms'] ?? '',
        $data['notes'] ?? '',
        $data['is_active'] ?? true
    ];
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $supplier_id = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'message' => 'Supplier created successfully',
            'supplier_id' => $supplier_id
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateSupplier($db, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Check if supplier exists
    $check_query = "SELECT id FROM suppliers WHERE id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$id]);
    if (!$check_stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Supplier not found']);
        return;
    }
    
    // Check if email conflicts with another supplier
    if (isset($data['email']) && !empty($data['email'])) {
        $email_check_query = "SELECT id FROM suppliers WHERE email = ? AND id != ?";
        $email_check_stmt = $db->prepare($email_check_query);
        $email_check_stmt->execute([$data['email'], $id]);
        if ($email_check_stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }
    }
    
    // Build update query dynamically
    $update_fields = [];
    $params = [];
    
    $allowed_fields = ['name', 'contact_person', 'email', 'phone', 'address', 'city', 'state', 
                      'postal_code', 'country', 'tax_id', 'payment_terms', 'notes', 'is_active'];
    
    foreach ($allowed_fields as $field) {
        if (isset($data[$field])) {
            $update_fields[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    if (empty($update_fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No valid fields to update']);
        return;
    }
    
    $update_fields[] = "updated_at = CURRENT_TIMESTAMP";
    $params[] = $id;
    
    $query = "UPDATE suppliers SET " . implode(', ', $update_fields) . " WHERE id = ?";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        echo json_encode(['message' => 'Supplier updated successfully']);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteSupplier($db, $id) {
    // Check if supplier has products
    $check_products_query = "SELECT COUNT(*) as count FROM products WHERE supplier_id = ? AND is_active = 1";
    $check_products_stmt = $db->prepare($check_products_query);
    $check_products_stmt->execute([$id]);
    $product_count = $check_products_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($product_count > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Cannot delete supplier with associated products. Please reassign or delete products first.']);
        return;
    }
    
    // Soft delete - set is_active to false
    $query = "UPDATE suppliers SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Supplier deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Supplier not found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Additional function to get supplier statistics
function getSupplierStats($db) {
    $query = "SELECT 
                COUNT(*) as total_suppliers,
                COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_suppliers,
                COUNT(CASE WHEN is_active = 0 THEN 1 END) as inactive_suppliers
              FROM suppliers";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($stats);
}

// Additional function to get top suppliers by purchase value
function getTopSuppliers($db) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    $query = "SELECT 
                s.id, s.name, s.contact_person, s.email,
                COALESCE(SUM(po.total_amount), 0) as total_purchase_value,
                COUNT(DISTINCT po.id) as purchase_order_count
              FROM suppliers s
              LEFT JOIN purchase_orders po ON s.id = po.supplier_id AND po.status != 'cancelled'
              GROUP BY s.id
              ORDER BY total_purchase_value DESC
              LIMIT ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$limit]);
    $top_suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($top_suppliers);
}
?>
