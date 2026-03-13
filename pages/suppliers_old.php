<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers - JIMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="JIMS Suppliers - Manage Supplier Relationships">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="../dashboard.php" class="logo">
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

    <!-- Main Container -->
    <div class="container">
        <div class="main-content">
            <!-- Sidebar -->
            <aside class="sidebar" id="sidebar">
                <nav class="sidebar-nav">
                    <ul class="sidebar-menu">
                        <li>
                            <a href="../dashboard.php" class="sidebar-link" data-page="dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="products.php" class="sidebar-link" data-page="products">
                                <i class="fas fa-box"></i>
                                <span>Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="sales.php" class="sidebar-link" data-page="sales">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Sales</span>
                            </a>
                        </li>
                        <li>
                            <a href="customers.php" class="sidebar-link" data-page="customers">
                                <i class="fas fa-users"></i>
                                <span>Customers</span>
                            </a>
                        </li>
                        <li>
                            <a href="suppliers.php" class="sidebar-link active" data-page="suppliers">
                                <i class="fas fa-truck"></i>
                                <span>Suppliers</span>
                            </a>
                        </li>
                        <li>
                            <a href="reports.php" class="sidebar-link" data-page="reports">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="settings.php" class="sidebar-link" data-page="settings">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="main-area">
                <div class="page-header">
                    <h1>Suppliers</h1>
                    <p>Manage supplier information and purchase orders</p>
                </div>

                <!-- Supplier Management -->
                <div class="content-section">
                    <div class="section-header">
                        <h2>Supplier Directory</h2>
                        <button class="btn btn-primary" id="addSupplierBtn">
                            <i class="fas fa-plus"></i> Add Supplier
                        </button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="search-filter">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search suppliers..." id="supplierSearch">
                        </div>
                        <div class="filter-options">
                            <select id="supplierFilter">
                                <option value="">All Suppliers</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="preferred">Preferred</option>
                            </select>
                        </div>
                    </div>

                    <!-- Suppliers Table -->
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company</th>
                                    <th>Contact</th>
                                    <th>Phone</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="suppliersTableBody">
                                <tr>
                                    <td colspan="7" class="no-data">
                                        <i class="fas fa-truck"></i>
                                        <p>No suppliers found. Add your first supplier to get started.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Purchase Orders -->
                <div class="content-section">
                    <h2>Recent Purchase Orders</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="no-data">
                                        <i class="fas fa-file-invoice"></i>
                                        <p>No purchase orders found. Create your first order to get started.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Supplier Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Suppliers</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Preferred</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Open Orders</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Purchases</h3>
                            <p class="stat-number">$0</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>JIMS</h4>
                    <p>Jewellery Inventory Management System</p>
                    <p>Version 1.0.0</p>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="../dashboard.php">Dashboard</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="sales.php">Sales</a></li>
                        <li><a href="reports.php">Reports</a></li>
                    </ul>
                </div>
                <div>
                    <h4>System Info</h4>
                    <p>Version: 1.0.0</p>
                    <p>Database: MySQL</p>
                    <p>Backend: PHP</p>
                    <p>Frontend: HTML/CSS/JS</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../assets/js/app.js"></script>
    <script>
        // Load suppliers on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.loadSuppliers === 'function') {
                window.loadSuppliers();
            }
        });
    </script>
</body>
</html>
