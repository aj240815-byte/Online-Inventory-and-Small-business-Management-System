<?php
// API Database Configuration
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'jims_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Products API
if (isset($_GET['endpoint']) && $_GET['endpoint'] === 'products') {
    switch ($method) {
        case 'GET':
            try {
                $stmt = $pdo->query("SELECT p.*, c.name as category_name, s.name as supplier_name 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    LEFT JOIN suppliers s ON p.supplier_id = s.id 
                                    WHERE p.is_active = 1");
                $products = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $products]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'POST':
            try {
                $sku = $input['sku'] ?? 'SKU-' . time();
                $stmt = $pdo->prepare("INSERT INTO products (sku, name, description, category_id, supplier_id, selling_price, stock_quantity, is_active) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([
                    $sku,
                    $input['name'],
                    $input['description'] ?? '',
                    $input['category_id'] ?? 1,
                    $input['supplier_id'] ?? null,
                    $input['price'] ?? 0,
                    $input['stock'] ?? 0
                ]);
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'PUT':
            try {
                $id = $input['id'];
                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, category_id = ?, supplier_id = ?, selling_price = ?, stock_quantity = ? WHERE id = ?");
                $stmt->execute([
                    $input['name'],
                    $input['description'] ?? '',
                    $input['category_id'] ?? 1,
                    $input['supplier_id'] ?? null,
                    $input['price'] ?? 0,
                    $input['stock'] ?? 0,
                    $id
                ]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'DELETE':
            try {
                $id = $input['id'] ?? $_GET['id'];
                $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
    }
    exit();
}

// Suppliers API
if (isset($_GET['endpoint']) && $_GET['endpoint'] === 'suppliers') {
    switch ($method) {
        case 'GET':
            try {
                $stmt = $pdo->query("SELECT * FROM suppliers WHERE is_active = 1");
                $suppliers = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $suppliers]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'POST':
            try {
                $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact_person, email, phone, address, city, payment_terms, notes, is_active) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([
                    $input['companyName'],
                    $input['contactPerson'],
                    $input['email'],
                    $input['phone'],
                    $input['address'] ?? '',
                    $input['city'] ?? '',
                    $input['paymentTerms'] ?? 'Net 30',
                    $input['notes'] ?? ''
                ]);
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'PUT':
            try {
                $id = $input['id'];
                $stmt = $pdo->prepare("UPDATE suppliers SET name = ?, contact_person = ?, email = ?, phone = ?, address = ?, city = ?, payment_terms = ?, notes = ? WHERE id = ?");
                $stmt->execute([
                    $input['companyName'],
                    $input['contactPerson'],
                    $input['email'],
                    $input['phone'],
                    $input['address'] ?? '',
                    $input['city'] ?? '',
                    $input['paymentTerms'] ?? 'Net 30',
                    $input['notes'] ?? '',
                    $id
                ]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'DELETE':
            try {
                $id = $input['id'] ?? $_GET['id'];
                $stmt = $pdo->prepare("UPDATE suppliers SET is_active = 0 WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
    }
    exit();
}

// Customers API
if (isset($_GET['endpoint']) && $_GET['endpoint'] === 'customers') {
    switch ($method) {
        case 'GET':
            try {
                $stmt = $pdo->query("SELECT * FROM customers WHERE is_active = 1");
                $customers = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $customers]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'POST':
            try {
                $fullName = $input['firstName'] . ' ' . $input['lastName'];
                $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, address, city, postal_code, notes, is_active) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([
                    $fullName,
                    $input['email'],
                    $input['phone'],
                    $input['address'] ?? '',
                    $input['city'] ?? '',
                    $input['zipCode'] ?? '',
                    $input['notes'] ?? ''
                ]);
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'PUT':
            try {
                $id = $input['id'];
                $fullName = $input['firstName'] . ' ' . $input['lastName'];
                $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, notes = ? WHERE id = ?");
                $stmt->execute([
                    $fullName,
                    $input['email'],
                    $input['phone'],
                    $input['address'] ?? '',
                    $input['city'] ?? '',
                    $input['zipCode'] ?? '',
                    $input['notes'] ?? '',
                    $id
                ]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'DELETE':
            try {
                $id = $input['id'] ?? $_GET['id'];
                $stmt = $pdo->prepare("UPDATE customers SET is_active = 0 WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
    }
    exit();
}

// Sales API
if (isset($_GET['endpoint']) && $_GET['endpoint'] === 'sales') {
    switch ($method) {
        case 'GET':
            try {
                $stmt = $pdo->query("SELECT s.*, c.name as customer_name 
                                    FROM sales s 
                                    LEFT JOIN customers c ON s.customer_id = c.id 
                                    ORDER BY s.created_at DESC");
                $sales = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $sales]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'POST':
            try {
                $pdo->beginTransaction();
                
                // Insert sale
                $stmt = $pdo->prepare("INSERT INTO sales (invoice_number, customer_id, sale_date, total_amount, payment_method, payment_status, notes) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['invoiceNumber'],
                    $input['customerId'] ?? null,
                    $input['date'],
                    $input['total'],
                    $input['paymentMethod'],
                    $input['status'] === 'Completed' ? 'paid' : 'pending',
                    $input['notes'] ?? ''
                ]);
                $saleId = $pdo->lastInsertId();
                
                // Insert sale items
                foreach ($input['items'] as $item) {
                    $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total) 
                                          VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $saleId,
                        $item['productId'],
                        $item['quantity'],
                        $item['price'],
                        $item['quantity'] * $item['price']
                    ]);
                    
                    // Update product stock
                    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['productId']]);
                }
                
                $pdo->commit();
                echo json_encode(['success' => true, 'id' => $saleId]);
            } catch (PDOException $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'DELETE':
            try {
                $id = $input['id'] ?? $_GET['id'];
                $stmt = $pdo->prepare("DELETE FROM sales WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
    }
    exit();
}

// If no endpoint matched
echo json_encode(['error' => 'Invalid endpoint']);
http_response_code(404);
