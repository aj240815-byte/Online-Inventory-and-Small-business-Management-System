<?php
/**
 * Reports API for JIMS
 * Handles analytics and reporting functions
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

$db = db_get_connection();

// Check database connection
if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    
    switch ($type) {
        case 'dashboard':
            getDashboardData($db);
            break;
        case 'sales':
            getSalesReport($db);
            break;
        case 'inventory':
            getInventoryReport($db);
            break;
        case 'suppliers':
            getSupplierReport($db);
            break;
        case 'profit':
            getProfitReport($db);
            break;
        case 'low_stock':
            getLowStockReport($db);
            break;
        case 'customer_analytics':
            getCustomerAnalytics($db);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid report type']);
            break;
    }
} elseif ($method === 'OPTIONS') {
    http_response_code(200);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function getDashboardData($db) {
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    
    // Get key metrics
    $metrics_query = "SELECT 
                        (SELECT COUNT(*) FROM products WHERE is_active = 1) as total_products,
                        (SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock_quantity <= reorder_level) as low_stock_items,
                        (SELECT COUNT(*) FROM suppliers WHERE is_active = 1) as total_suppliers,
                        (SELECT COUNT(*) FROM customers WHERE is_active = 1) as total_customers,
                        (SELECT COUNT(*) FROM sales WHERE sale_date BETWEEN ? AND ?) as total_sales,
                        (SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE sale_date BETWEEN ? AND ?) as total_revenue,
                        (SELECT COALESCE(AVG(total_amount), 0) FROM sales WHERE sale_date BETWEEN ? AND ?) as avg_sale_value";
    
    $metrics_stmt = $db->prepare($metrics_query);
    $metrics_stmt->execute([$date_from, $date_to, $date_from, $date_to, $date_from, $date_to]);
    $metrics = $metrics_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get recent sales
    $recent_sales_query = "SELECT s.id, s.invoice_number, s.sale_date, s.total_amount, s.payment_status,
                           c.name as customer_name
                           FROM sales s
                           LEFT JOIN customers c ON s.customer_id = c.id
                           ORDER BY s.created_at DESC
                           LIMIT 5";
    
    $recent_sales_stmt = $db->prepare($recent_sales_query);
    $recent_sales_stmt->execute();
    $recent_sales = $recent_sales_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get top selling products
    $top_products_query = "SELECT p.id, p.name, p.sku, p.stock_quantity,
                           COALESCE(SUM(si.quantity), 0) as total_sold
                           FROM products p
                           LEFT JOIN sale_items si ON p.id = si.product_id
                           LEFT JOIN sales s ON si.sale_id = s.id AND s.sale_date BETWEEN ? AND ?
                           WHERE p.is_active = 1
                           GROUP BY p.id
                           ORDER BY total_sold DESC
                           LIMIT 5";
    
    $top_products_stmt = $db->prepare($top_products_query);
    $top_products_stmt->execute([$date_from, $date_to]);
    $top_products = $top_products_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get low stock items
    $low_stock_query = "SELECT id, name, sku, stock_quantity, reorder_level
                        FROM products
                        WHERE is_active = 1 AND stock_quantity <= reorder_level
                        ORDER BY stock_quantity ASC
                        LIMIT 5";
    
    $low_stock_stmt = $db->prepare($low_stock_query);
    $low_stock_stmt->execute();
    $low_stock = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get sales trend (last 7 days)
    $sales_trend_query = "SELECT DATE(sale_date) as date, COUNT(*) as sales_count, SUM(total_amount) as revenue
                         FROM sales
                         WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                         GROUP BY DATE(sale_date)
                         ORDER BY date ASC";
    
    $sales_trend_stmt = $db->prepare($sales_trend_query);
    $sales_trend_stmt->execute();
    $sales_trend = $sales_trend_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'metrics' => $metrics,
        'recent_sales' => $recent_sales,
        'top_products' => $top_products,
        'low_stock' => $low_stock,
        'sales_trend' => $sales_trend,
        'period' => ['from' => $date_from, 'to' => $date_to]
    ]);
}

function getSalesReport($db) {
    $period = isset($_GET['period']) ? $_GET['period'] : 'monthly';
    $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
    $month = isset($_GET['month']) ? (int)$_GET['month'] : null;
    
    if ($period === 'daily' && $month) {
        $date_format = '%Y-%m-%d';
        $group_by = 'DATE(sale_date)';
        $where_clause = "WHERE YEAR(sale_date) = ? AND MONTH(sale_date) = ?";
        $params = [$year, $month];
    } elseif ($period === 'monthly') {
        $date_format = '%Y-%m';
        $group_by = 'YEAR(sale_date), MONTH(sale_date)';
        $where_clause = "WHERE YEAR(sale_date) = ?";
        $params = [$year];
    } else {
        $date_format = '%Y';
        $group_by = 'YEAR(sale_date)';
        $where_clause = "WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)";
        $params = [];
    }
    
    $query = "SELECT 
                DATE_FORMAT(sale_date, '$date_format') as period,
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                SUM(subtotal) as gross_revenue,
                SUM(tax_amount) as total_tax,
                SUM(discount_amount) as total_discount,
                AVG(total_amount) as avg_sale_value,
                COUNT(DISTINCT customer_id) as unique_customers
              FROM sales
              $where_clause
              GROUP BY $group_by
              ORDER BY period DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get sales by category
    $category_query = "SELECT 
                        c.name as category,
                        COUNT(DISTINCT s.id) as sales_count,
                        SUM(si.quantity) as items_sold,
                        SUM(si.line_total) as revenue
                      FROM sales s
                      JOIN sale_items si ON s.id = si.sale_id
                      JOIN products p ON si.product_id = p.id
                      JOIN categories c ON p.category_id = c.id
                      $where_clause
                      GROUP BY c.id
                      ORDER BY revenue DESC";
    
    $category_stmt = $db->prepare($category_query);
    $category_stmt->execute($params);
    $category_data = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get payment method breakdown
    $payment_query = "SELECT 
                        payment_method,
                        COUNT(*) as count,
                        SUM(total_amount) as total
                      FROM sales
                      $where_clause
                      GROUP BY payment_method
                      ORDER BY total DESC";
    
    $payment_stmt = $db->prepare($payment_query);
    $payment_stmt->execute($params);
    $payment_data = $payment_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'sales_trend' => $sales_data,
        'by_category' => $category_data,
        'by_payment_method' => $payment_data,
        'period' => $period,
        'filters' => ['year' => $year, 'month' => $month]
    ]);
}

function getInventoryReport($db) {
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    
    $where_clause = "WHERE p.is_active = 1";
    $params = [];
    
    if ($category_id) {
        $where_clause .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    // Get inventory summary
    $summary_query = "SELECT 
                        COUNT(*) as total_products,
                        SUM(stock_quantity) as total_items,
                        SUM(stock_quantity * cost_price) as total_cost_value,
                        SUM(stock_quantity * selling_price) as total_sell_value,
                        COUNT(CASE WHEN stock_quantity <= reorder_level THEN 1 END) as low_stock_count,
                        COUNT(CASE WHEN stock_quantity = 0 THEN 1 END) as out_of_stock_count
                      FROM products p
                      $where_clause";
    
    $summary_stmt = $db->prepare($summary_query);
    $summary_stmt->execute($params);
    $summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get inventory by category
    $category_query = "SELECT 
                        c.name as category,
                        COUNT(p.id) as product_count,
                        SUM(p.stock_quantity) as total_quantity,
                        SUM(p.stock_quantity * p.cost_price) as total_cost,
                        SUM(p.stock_quantity * p.selling_price) as total_value
                      FROM products p
                      JOIN categories c ON p.category_id = c.id
                      $where_clause
                      GROUP BY c.id
                      ORDER BY total_value DESC";
    
    $category_stmt = $db->prepare($category_query);
    $category_stmt->execute($params);
    $category_data = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get slow moving items (no sales in last 90 days)
    $slow_moving_query = "SELECT 
                            p.id, p.name, p.sku, p.stock_quantity, p.cost_price, p.selling_price,
                            p.last_sale_date
                          FROM products p
                          LEFT JOIN (
                            SELECT DISTINCT si.product_id, MAX(s.sale_date) as last_sale_date
                            FROM sale_items si
                            JOIN sales s ON si.sale_id = s.id
                            WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                            GROUP BY si.product_id
                          ) recent_sales ON p.id = recent_sales.product_id
                          WHERE p.is_active = 1 AND p.stock_quantity > 0 
                          AND (recent_sales.last_sale_date IS NULL OR recent_sales.last_sale_date < DATE_SUB(CURDATE(), INTERVAL 90 DAY))
                          ORDER BY p.stock_quantity * p.cost_price DESC
                          LIMIT 20";
    
    $slow_moving_stmt = $db->prepare($slow_moving_query);
    $slow_moving_stmt->execute();
    $slow_moving = $slow_moving_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'summary' => $summary,
        'by_category' => $category_data,
        'slow_moving' => $slow_moving
    ]);
}

function getSupplierReport($db) {
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    
    // Get supplier performance
    $performance_query = "SELECT 
                            s.id, s.name, s.contact_person, s.email,
                            COUNT(DISTINCT p.id) as product_count,
                            COUNT(DISTINCT po.id) as purchase_order_count,
                            COALESCE(SUM(po.total_amount), 0) as total_purchase_value,
                            AVG(po.total_amount) as avg_order_value
                          FROM suppliers s
                          LEFT JOIN products p ON s.id = p.supplier_id AND p.is_active = 1
                          LEFT JOIN purchase_orders po ON s.id = po.supplier_id 
                            AND po.order_date BETWEEN ? AND ? AND po.status != 'cancelled'
                          WHERE s.is_active = 1
                          GROUP BY s.id
                          ORDER BY total_purchase_value DESC";
    
    $performance_stmt = $db->prepare($performance_query);
    $performance_stmt->execute([$date_from, $date_to]);
    $performance_data = $performance_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get purchase order trends
    $po_trend_query = "SELECT 
                        DATE_FORMAT(order_date, '%Y-%m') as period,
                        COUNT(*) as order_count,
                        SUM(total_amount) as total_value
                      FROM purchase_orders
                      WHERE order_date BETWEEN ? AND ? AND status != 'cancelled'
                      GROUP BY DATE_FORMAT(order_date, '%Y-%m')
                      ORDER BY period DESC";
    
    $po_trend_stmt = $db->prepare($po_trend_query);
    $po_trend_stmt->execute([$date_from, $date_to]);
    $po_trend = $po_trend_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'supplier_performance' => $performance_data,
        'purchase_order_trends' => $po_trend,
        'period' => ['from' => $date_from, 'to' => $date_to]
    ]);
}

function getProfitReport($db) {
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    
    // Get profit analysis
    $profit_query = "SELECT 
                        DATE_FORMAT(s.sale_date, '%Y-%m') as period,
                        SUM(s.total_amount) as revenue,
                        SUM(si.quantity * p.cost_price) as cost_of_goods_sold,
                        SUM(s.total_amount) - SUM(si.quantity * p.cost_price) as gross_profit,
                        (SUM(s.total_amount) - SUM(si.quantity * p.cost_price)) / SUM(s.total_amount) * 100 as profit_margin
                      FROM sales s
                      JOIN sale_items si ON s.id = si.sale_id
                      JOIN products p ON si.product_id = p.id
                      WHERE s.sale_date BETWEEN ? AND ?
                      GROUP BY DATE_FORMAT(s.sale_date, '%Y-%m')
                      ORDER BY period DESC";
    
    $profit_stmt = $db->prepare($profit_query);
    $profit_stmt->execute([$date_from, $date_to]);
    $profit_data = $profit_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get profit by category
    $category_profit_query = "SELECT 
                                c.name as category,
                                SUM(s.total_amount) as revenue,
                                SUM(si.quantity * p.cost_price) as cost_of_goods_sold,
                                SUM(s.total_amount) - SUM(si.quantity * p.cost_price) as gross_profit,
                                (SUM(s.total_amount) - SUM(si.quantity * p.cost_price)) / SUM(s.total_amount) * 100 as profit_margin
                              FROM sales s
                              JOIN sale_items si ON s.id = si.sale_id
                              JOIN products p ON si.product_id = p.id
                              JOIN categories c ON p.category_id = c.id
                              WHERE s.sale_date BETWEEN ? AND ?
                              GROUP BY c.id
                              ORDER BY gross_profit DESC";
    
    $category_profit_stmt = $db->prepare($category_profit_query);
    $category_profit_stmt->execute([$date_from, $date_to]);
    $category_profit = $category_profit_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get profit by product (top 20)
    $product_profit_query = "SELECT 
                              p.name, p.sku,
                              SUM(si.quantity) as quantity_sold,
                              SUM(s.total_amount) as revenue,
                              SUM(si.quantity * p.cost_price) as cost_of_goods_sold,
                              SUM(s.total_amount) - SUM(si.quantity * p.cost_price) as gross_profit,
                              (SUM(s.total_amount) - SUM(si.quantity * p.cost_price)) / SUM(s.total_amount) * 100 as profit_margin
                            FROM sales s
                            JOIN sale_items si ON s.id = si.sale_id
                            JOIN products p ON si.product_id = p.id
                            WHERE s.sale_date BETWEEN ? AND ?
                            GROUP BY p.id
                            ORDER BY gross_profit DESC
                            LIMIT 20";
    
    $product_profit_stmt = $db->prepare($product_profit_query);
    $product_profit_stmt->execute([$date_from, $date_to]);
    $product_profit = $product_profit_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'profit_trend' => $profit_data,
        'by_category' => $category_profit,
        'by_product' => $product_profit,
        'period' => ['from' => $date_from, 'to' => $date_to]
    ]);
}

function getLowStockReport($db) {
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    
    $where_clause = "WHERE p.is_active = 1 AND p.stock_quantity <= p.reorder_level";
    $params = [];
    
    if ($category_id) {
        $where_clause .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    $query = "SELECT 
                p.id, p.name, p.sku, p.stock_quantity, p.reorder_level, p.minimum_stock,
                p.cost_price, p.selling_price,
                c.name as category_name,
                s.name as supplier_name,
                (p.reorder_level - p.stock_quantity) as needed_to_reorder,
                (p.reorder_level - p.stock_quantity) * p.cost_price as reorder_cost
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN suppliers s ON p.supplier_id = s.id
              $where_clause
              ORDER BY (p.reorder_level - p.stock_quantity) DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'low_stock_items' => $low_stock_items,
        'total_items' => count($low_stock_items)
    ]);
}

function getCustomerAnalytics($db) {
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    
    // Get top customers by revenue
    $top_customers_query = "SELECT 
                              c.id, c.name, c.email, c.phone,
                              COUNT(DISTINCT s.id) as purchase_count,
                              SUM(s.total_amount) as total_spent,
                              AVG(s.total_amount) as avg_purchase,
                              MAX(s.sale_date) as last_purchase_date
                            FROM customers c
                            JOIN sales s ON c.id = s.customer_id
                            WHERE s.sale_date BETWEEN ? AND ?
                            GROUP BY c.id
                            ORDER BY total_spent DESC
                            LIMIT 20";
    
    $top_customers_stmt = $db->prepare($top_customers_query);
    $top_customers_stmt->execute([$date_from, $date_to]);
    $top_customers = $top_customers_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get customer segments
    $segment_query = "SELECT 
                        CASE 
                          WHEN total_spent >= 10000 THEN 'VIP'
                          WHEN total_spent >= 5000 THEN 'Premium'
                          WHEN total_spent >= 1000 THEN 'Regular'
                          ELSE 'Occasional'
                        END as segment,
                        COUNT(*) as customer_count,
                        SUM(total_spent) as segment_revenue,
                        AVG(total_spent) as avg_customer_value
                      FROM (
                        SELECT c.id, SUM(s.total_amount) as total_spent
                        FROM customers c
                        JOIN sales s ON c.id = s.customer_id
                        WHERE s.sale_date BETWEEN ? AND ?
                        GROUP BY c.id
                      ) customer_data
                      GROUP BY segment
                      ORDER BY segment_revenue DESC";
    
    $segment_stmt = $db->prepare($segment_query);
    $segment_stmt->execute([$date_from, $date_to]);
    $segments = $segment_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'top_customers' => $top_customers,
        'customer_segments' => $segments,
        'period' => ['from' => $date_from, 'to' => $date_to]
    ]);
}
?>
