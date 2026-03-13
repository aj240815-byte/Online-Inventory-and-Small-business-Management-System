<?php
/**
 * Sales API for JIMS
 * Handles sales transactions and reporting
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
            getSale($db, $_GET['id']);
        } elseif (isset($_GET['report'])) {
            getSalesReport($db);
        } else {
            getSales($db);
        }
        break;
    case 'POST':
        createSale($db);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            updateSale($db, $_GET['id']);
        } else {
            echo json_encode(['error' => 'Sale ID required']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteSale($db, $_GET['id']);
        } else {
            echo json_encode(['error' => 'Sale ID required']);
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

function getSales($db) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    
    // Build WHERE clause
    $where = [];
    $params = [];
    
    if (!empty($search)) {
        $where[] = '(s.invoice_number LIKE ? OR c.name LIKE ? OR c.email LIKE ?)';
        $search_param = "%$search%";
        array_push($params, $search_param, $search_param, $search_param);
    }
    
    if ($customer_id) {
        $where[] = 's.customer_id = ?';
        $params[] = $customer_id;
    }
    
    if ($status) {
        $where[] = 's.payment_status = ?';
        $params[] = $status;
    }
    
    if ($date_from) {
        $where[] = 's.sale_date >= ?';
        $params[] = $date_from;
    }
    
    if ($date_to) {
        $where[] = 's.sale_date <= ?';
        $params[] = $date_to;
    }
    
    $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM sales s 
                    LEFT JOIN customers c ON s.customer_id = c.id 
                    $where_clause";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get sales
    $query = "SELECT s.*, c.name as customer_name, c.email as customer_email,
              (SELECT COUNT(*) FROM sale_items si WHERE si.sale_id = s.id) as item_count
              FROM sales s 
              LEFT JOIN customers c ON s.customer_id = c.id 
              $where_clause 
              ORDER BY s.sale_date DESC, s.created_at DESC 
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'sales' => $sales,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

function getSale($db, $id) {
    // Get sale details
    $query = "SELECT s.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
              c.address as customer_address
              FROM sales s 
              LEFT JOIN customers c ON s.customer_id = c.id 
              WHERE s.id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$sale) {
        http_response_code(404);
        echo json_encode(['error' => 'Sale not found']);
        return;
    }
    
    // Get sale items
    $items_query = "SELECT si.*, p.name as product_name, p.sku, p.images
                    FROM sale_items si 
                    LEFT JOIN products p ON si.product_id = p.id 
                    WHERE si.sale_id = ?";
    
    $items_stmt = $db->prepare($items_query);
    $items_stmt->execute([$id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sale['items'] = $items;
    
    echo json_encode($sale);
}

function createSale($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Validate required fields
    if (!isset($data['items']) || empty($data['items'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Sale items are required']);
        return;
    }
    
    try {
        $db->beginTransaction();
        
        // Generate invoice number
        $invoice_number = generateInvoiceNumber($db);
        
        // Calculate totals
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $line_total = $item['quantity'] * $item['unit_price'];
            $discount_amount = $line_total * ($item['discount_percentage'] / 100);
            $subtotal += ($line_total - $discount_amount);
        }
        
        $tax_rate = $data['tax_rate'] ?? 0;
        $tax_amount = $subtotal * ($tax_rate / 100);
        $discount_amount = $data['discount_amount'] ?? 0;
        $total_amount = $subtotal + $tax_amount - $discount_amount;
        
        // Insert sale
        $sale_query = "INSERT INTO sales (invoice_number, customer_id, sale_date, subtotal, 
                       tax_rate, tax_amount, discount_amount, total_amount, payment_method, 
                       payment_status, notes, created_by) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $sale_params = [
            $invoice_number,
            $data['customer_id'] ?? null,
            $data['sale_date'] ?? date('Y-m-d'),
            $subtotal,
            $tax_rate,
            $tax_amount,
            $discount_amount,
            $total_amount,
            $data['payment_method'] ?? 'cash',
            $data['payment_status'] ?? 'pending',
            $data['notes'] ?? '',
            $data['created_by'] ?? 'system'
        ];
        
        $sale_stmt = $db->prepare($sale_query);
        $sale_stmt->execute($sale_params);
        $sale_id = $db->lastInsertId();
        
        // Insert sale items and update inventory
        foreach ($data['items'] as $item) {
            // Calculate line total
            $line_total = $item['quantity'] * $item['unit_price'];
            $discount_amount = $line_total * ($item['discount_percentage'] / 100);
            $final_line_total = $line_total - $discount_amount;
            
            // Insert sale item
            $item_query = "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, 
                          discount_percentage, line_total) VALUES (?, ?, ?, ?, ?, ?)";
            
            $item_params = [
                $sale_id,
                $item['product_id'],
                $item['quantity'],
                $item['unit_price'],
                $item['discount_percentage'] ?? 0,
                $final_line_total
            ];
            
            $item_stmt = $db->prepare($item_query);
            $item_stmt->execute($item_params);
            
            // Update product stock
            $update_stock_query = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
            $update_stock_stmt = $db->prepare($update_stock_query);
            $result = $update_stock_stmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
            
            if (!$result || $update_stock_stmt->rowCount() === 0) {
                throw new Exception("Insufficient stock for product ID: {$item['product_id']}");
            }
            
            // Record inventory movement
            recordInventoryMovement($db, $item['product_id'], 'out', $item['quantity'], 'sale', $sale_id, "Sale #$invoice_number");
        }
        
        $db->commit();
        
        http_response_code(201);
        echo json_encode([
            'message' => 'Sale created successfully',
            'sale_id' => $sale_id,
            'invoice_number' => $invoice_number,
            'total_amount' => $total_amount
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create sale: ' . $e->getMessage()]);
    }
}

function updateSale($db, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Check if sale exists
    $check_query = "SELECT id, payment_status FROM sales WHERE id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$id]);
    $existing_sale = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existing_sale) {
        http_response_code(404);
        echo json_encode(['error' => 'Sale not found']);
        return;
    }
    
    // Build update query dynamically
    $update_fields = [];
    $params = [];
    
    $allowed_fields = ['customer_id', 'payment_method', 'payment_status', 'notes'];
    
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
    
    $query = "UPDATE sales SET " . implode(', ', $update_fields) . " WHERE id = ?";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        echo json_encode(['message' => 'Sale updated successfully']);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteSale($db, $id) {
    try {
        $db->beginTransaction();
        
        // Get sale items to restore stock
        $items_query = "SELECT product_id, quantity FROM sale_items WHERE sale_id = ?";
        $items_stmt = $db->prepare($items_query);
        $items_stmt->execute([$id]);
        $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Restore stock for each item
        foreach ($items as $item) {
            $update_stock_query = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
            $update_stock_stmt = $db->prepare($update_stock_query);
            $update_stock_stmt->execute([$item['quantity'], $item['product_id']]);
            
            // Record inventory movement
            recordInventoryMovement($db, $item['product_id'], 'in', $item['quantity'], 'sale', $id, "Sale cancellation");
        }
        
        // Delete sale items
        $delete_items_query = "DELETE FROM sale_items WHERE sale_id = ?";
        $delete_items_stmt = $db->prepare($delete_items_query);
        $delete_items_stmt->execute([$id]);
        
        // Delete sale
        $delete_sale_query = "DELETE FROM sales WHERE id = ?";
        $delete_sale_stmt = $db->prepare($delete_sale_query);
        $delete_sale_stmt->execute([$id]);
        
        $db->commit();
        
        echo json_encode(['message' => 'Sale deleted successfully']);
        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete sale: ' . $e->getMessage()]);
    }
}

function getSalesReport($db) {
    $type = isset($_GET['type']) ? $_GET['type'] : 'daily';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    
    $group_by = $type === 'daily' ? 'DATE(s.sale_date)' : 'YEAR(s.sale_date), MONTH(s.sale_date)';
    $date_format = $type === 'daily' ? '%Y-%m-%d' : '%Y-%m';
    
    $query = "SELECT 
                DATE_FORMAT(s.sale_date, '$date_format') as period,
                COUNT(*) as total_sales,
                SUM(s.total_amount) as total_revenue,
                SUM(s.subtotal) as gross_revenue,
                SUM(s.tax_amount) as total_tax,
                SUM(s.discount_amount) as total_discount,
                AVG(s.total_amount) as avg_sale_value,
                (SELECT COUNT(DISTINCT customer_id) FROM sales WHERE sale_date BETWEEN ? AND ? AND customer_id IS NOT NULL) as unique_customers
              FROM sales s 
              WHERE s.sale_date BETWEEN ? AND ?
              GROUP BY $group_by
              ORDER BY period DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$date_from, $date_to, $date_from, $date_to]);
    $report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get top selling products
    $top_products_query = "SELECT 
                              p.name, p.sku,
                              SUM(si.quantity) as total_quantity,
                              SUM(si.line_total) as total_revenue
                            FROM sale_items si
                            JOIN sales s ON si.sale_id = s.id
                            JOIN products p ON si.product_id = p.id
                            WHERE s.sale_date BETWEEN ? AND ?
                            GROUP BY si.product_id
                            ORDER BY total_quantity DESC
                            LIMIT 10";
    
    $top_products_stmt = $db->prepare($top_products_query);
    $top_products_stmt->execute([$date_from, $date_to]);
    $top_products = $top_products_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get summary statistics
    $summary_query = "SELECT 
                        COUNT(*) as total_transactions,
                        SUM(s.total_amount) as total_revenue,
                        AVG(s.total_amount) as average_transaction,
                        MAX(s.total_amount) as largest_sale,
                        MIN(s.total_amount) as smallest_sale
                      FROM sales s 
                      WHERE s.sale_date BETWEEN ? AND ?";
    
    $summary_stmt = $db->prepare($summary_query);
    $summary_stmt->execute([$date_from, $date_to]);
    $summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'report_type' => $type,
        'period' => ['from' => $date_from, 'to' => $date_to],
        'summary' => $summary,
        'data' => $report_data,
        'top_products' => $top_products
    ]);
}

function generateInvoiceNumber($db) {
    $prefix = 'INV';
    $date = date('Ymd');
    
    // Get today's invoice count
    $query = "SELECT COUNT(*) as count FROM sales WHERE DATE(created_at) = CURDATE()";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $date . $sequence;
}

function recordInventoryMovement($db, $product_id, $movement_type, $quantity, $reference_type, $reference_id, $notes) {
    $query = "INSERT INTO inventory_movements (product_id, movement_type, quantity, reference_type, reference_id, notes) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$product_id, $movement_type, $quantity, $reference_type, $reference_id, $notes]);
}
?>
