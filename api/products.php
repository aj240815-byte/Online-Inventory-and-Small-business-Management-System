<?php
/**
 * Products API for JIMS
 * Handles CRUD operations for products/inventory
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
            getProduct($db, $_GET['id']);
        } else {
            getProducts($db);
        }
        break;
    case 'POST':
        createProduct($db);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            updateProduct($db, $_GET['id']);
        } else {
            echo json_encode(['error' => 'Product ID required']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteProduct($db, $_GET['id']);
        } else {
            echo json_encode(['error' => 'Product ID required']);
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

function getProducts($db) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $category = isset($_GET['category']) ? (int)$_GET['category'] : '';
    $supplier = isset($_GET['supplier']) ? (int)$_GET['supplier'] : '';
    $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
    
    // Build WHERE clause
    $where = ['p.is_active = 1'];
    $params = [];
    
    if (!empty($search)) {
        $where[] = '(p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)';
        $search_param = "%$search%";
        array_push($params, $search_param, $search_param, $search_param);
    }
    
    if ($category) {
        $where[] = 'p.category_id = ?';
        $params[] = $category;
    }
    
    if ($supplier) {
        $where[] = 'p.supplier_id = ?';
        $params[] = $supplier;
    }
    
    $where[] = 'p.selling_price BETWEEN ? AND ?';
    $params[] = $min_price;
    $params[] = $max_price;
    
    $where_clause = 'WHERE ' . implode(' AND ', $where);
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM products p $where_clause";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get products
    $query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN suppliers s ON p.supplier_id = s.id 
              $where_clause 
              ORDER BY p.created_at DESC 
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'products' => $products,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

function getProduct($db, $id) {
    $query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN suppliers s ON p.supplier_id = s.id 
              WHERE p.id = ? AND p.is_active = 1";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
}

function createProduct($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Validate required fields
    $required_fields = ['sku', 'name', 'cost_price', 'selling_price'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            return;
        }
    }
    
    // Check if SKU already exists
    $check_query = "SELECT id FROM products WHERE sku = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$data['sku']]);
    if ($check_stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'SKU already exists']);
        return;
    }
    
    // Calculate markup percentage
    $markup_percentage = 0;
    if ($data['cost_price'] > 0) {
        $markup_percentage = (($data['selling_price'] - $data['cost_price']) / $data['cost_price']) * 100;
    }
    
    $query = "INSERT INTO products (sku, name, description, category_id, supplier_id, 
              material_type, metal_type, gemstone_type, weight, carat, color, clarity, cut, 
              dimensions, cost_price, selling_price, markup_percentage, stock_quantity, 
              minimum_stock, maximum_stock, reorder_level, location, barcode, images) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $data['sku'],
        $data['name'],
        $data['description'] ?? '',
        $data['category_id'] ?? null,
        $data['supplier_id'] ?? null,
        $data['material_type'] ?? '',
        $data['metal_type'] ?? '',
        $data['gemstone_type'] ?? '',
        $data['weight'] ?? 0,
        $data['carat'] ?? 0,
        $data['color'] ?? '',
        $data['clarity'] ?? '',
        $data['cut'] ?? '',
        $data['dimensions'] ?? '',
        $data['cost_price'],
        $data['selling_price'],
        $markup_percentage,
        $data['stock_quantity'] ?? 0,
        $data['minimum_stock'] ?? 5,
        $data['maximum_stock'] ?? 100,
        $data['reorder_level'] ?? 10,
        $data['location'] ?? '',
        $data['barcode'] ?? '',
        $data['images'] ?? ''
    ];
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $product_id = $db->lastInsertId();
        
        // Record inventory movement
        recordInventoryMovement($db, $product_id, 'in', $data['stock_quantity'] ?? 0, 'purchase', null, 'Initial stock');
        
        http_response_code(201);
        echo json_encode([
            'message' => 'Product created successfully',
            'product_id' => $product_id
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateProduct($db, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Check if product exists
    $check_query = "SELECT id, stock_quantity FROM products WHERE id = ? AND is_active = 1";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$id]);
    $existing_product = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existing_product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        return;
    }
    
    // Check if SKU conflicts with another product
    if (isset($data['sku'])) {
        $sku_check_query = "SELECT id FROM products WHERE sku = ? AND id != ?";
        $sku_check_stmt = $db->prepare($sku_check_query);
        $sku_check_stmt->execute([$data['sku'], $id]);
        if ($sku_check_stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'SKU already exists']);
            return;
        }
    }
    
    // Recalculate markup if prices changed
    if (isset($data['cost_price']) && isset($data['selling_price'])) {
        if ($data['cost_price'] > 0) {
            $data['markup_percentage'] = (($data['selling_price'] - $data['cost_price']) / $data['cost_price']) * 100;
        }
    }
    
    // Build update query dynamically
    $update_fields = [];
    $params = [];
    
    $allowed_fields = ['sku', 'name', 'description', 'category_id', 'supplier_id', 
                      'material_type', 'metal_type', 'gemstone_type', 'weight', 'carat', 
                      'color', 'clarity', 'cut', 'dimensions', 'cost_price', 'selling_price', 
                      'markup_percentage', 'minimum_stock', 'maximum_stock', 'reorder_level', 
                      'location', 'barcode', 'images'];
    
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
    
    $query = "UPDATE products SET " . implode(', ', $update_fields) . " WHERE id = ?";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        // Record inventory movement if stock quantity changed
        if (isset($data['stock_quantity']) && $data['stock_quantity'] != $existing_product['stock_quantity']) {
            $difference = $data['stock_quantity'] - $existing_product['stock_quantity'];
            $movement_type = $difference > 0 ? 'in' : 'out';
            recordInventoryMovement($db, $id, $movement_type, abs($difference), 'adjustment', null, 'Manual adjustment');
            
            // Update stock quantity
            $stock_query = "UPDATE products SET stock_quantity = ? WHERE id = ?";
            $stock_stmt = $db->prepare($stock_query);
            $stock_stmt->execute([$data['stock_quantity'], $id]);
        }
        
        echo json_encode(['message' => 'Product updated successfully']);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteProduct($db, $id) {
    // Soft delete - set is_active to false
    $query = "UPDATE products SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function recordInventoryMovement($db, $product_id, $movement_type, $quantity, $reference_type, $reference_id, $notes) {
    $query = "INSERT INTO inventory_movements (product_id, movement_type, quantity, reference_type, reference_id, notes) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$product_id, $movement_type, $quantity, $reference_type, $reference_id, $notes]);
}
?>
