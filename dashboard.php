<?php
session_start();

// Debug: Log session status
error_log("Dashboard accessed - Session ID: " . session_id());
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    error_log("User not logged in, redirecting to login.php");
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    error_log("Logout requested");
    session_destroy();
    header('Location: login.php');
    exit();
}

error_log("User authenticated, loading dashboard");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JIMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="JIMS Dashboard - Overview and Statistics">
    <style>
        body {
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .main-content {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        
        .sidebar {
            width: 250px;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: #495057;
            border-left: 3px solid transparent;
        }
        
        .sidebar-link.active {
            background-color: #f8f9fa;
            border-left-color: #2c3e50;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .sidebar-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .main-area {
            flex: 1;
            padding: 2rem;
            background: #f8f9fa;
        }
        
        .dashboard-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .dashboard-header h1 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .dashboard-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 20px;
            background: #f8f9fa;
            color: #495057;
        }
        
        .stat-content h3 {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }
        
        .section {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .section h2 {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin: 0 auto 15px;
            background: white;
            color: #495057;
            border: 1px solid #e9ecef;
        }
        
        .action-card h3 {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .action-card p {
            font-size: 0.85rem;
            color: #6c757d;
            margin: 0;
        }
        
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            background: white;
            color: #495057;
            border: 1px solid #e9ecef;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-content h4 {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .activity-content p {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="dashboard.php" class="logo">
                    <i class="fas fa-gem"></i>
                    <span>JIMS</span>
                </a>
                <nav>
                    <div class="nav-actions">
                        <span class="nav-user">
                            <i class="fas fa-user-circle"></i> 
                            <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
                        </span>
                        <a href="?logout=1" class="logout-btn" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                        <button class="mobile-menu-toggle" id="mobileMenuToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Container with Sidebar -->
    <div class="container">
        <div class="main-content">
            <!-- Sidebar -->
            <aside class="sidebar" id="sidebar">
                <nav class="sidebar-nav">
                    <ul class="sidebar-menu">
                        <li>
                            <a href="dashboard.php" class="sidebar-link active" data-page="dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/products.php" class="sidebar-link" data-page="products">
                                <i class="fas fa-box"></i>
                                <span>Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/sales.php" class="sidebar-link" data-page="sales">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Sales</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/customers.php" class="sidebar-link" data-page="customers">
                                <i class="fas fa-users"></i>
                                <span>Customers</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/suppliers.php" class="sidebar-link" data-page="suppliers">
                                <i class="fas fa-truck"></i>
                                <span>Suppliers</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/reports.php" class="sidebar-link" data-page="reports">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="pages/settings.php" class="sidebar-link" data-page="settings">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="main-area">
                <!-- Dashboard Header -->
                <div class="dashboard-header">
                    <h1>JIMS Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>!</p>
                </div>

                <!-- Business Overview Section -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Products</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Sales</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Customers</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Revenue</h3>
                            <p class="stat-number">UGX 0</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="section">
                    <h2>Quick Actions</h2>
                    <div class="action-grid">
                        <a href="pages/products.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h3>Add Product</h3>
                            <p>Manage inventory</p>
                        </a>
                        <a href="pages/sales.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-cash-register"></i>
                            </div>
                            <h3>Process Sale</h3>
                            <p>New transaction</p>
                        </a>
                        <a href="pages/customers.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h3>Add Customer</h3>
                            <p>New customer</p>
                        </a>
                        <a href="pages/reports.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h3>View Reports</h3>
                            <p>Analytics</p>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="section">
                    <h2>Recent Activity</h2>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="activity-content">
                                <h4>Welcome to JIMS</h4>
                                <p>Start by adding your first product to begin managing your jewellery inventory.</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div class="activity-content">
                                <h4>Getting Started</h4>
                                <p>Use the Products page to add your jewellery items and manage stock levels.</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="activity-content">
                                <h4>Sales Analytics</h4>
                                <p>Your sales data and reports will appear here once you start processing orders.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #2c3e50; color: white; padding: 20px 0; margin-top: auto;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin: 0; font-size: 0.9rem;">© 2024 JIMS - Jewellery Inventory Management System</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/app.js"></script>
    <script>
        // Simplified dashboard initialization
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded successfully');
        });
    </script>
</body>
</html>
