<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - JIMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="JIMS Reports - Analytics and Insights">
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
                            <a href="suppliers.php" class="sidebar-link" data-page="suppliers">
                                <i class="fas fa-truck"></i>
                                <span>Suppliers</span>
                            </a>
                        </li>
                        <li>
                            <a href="reports.php" class="sidebar-link active" data-page="reports">
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
                    <h1>Reports</h1>
                    <p>Analytics and business insights</p>
                </div>

                <!-- Report Filters -->
                <div class="content-section">
                    <div class="section-header">
                        <h2>Generate Reports</h2>
                        <div class="filter-options">
                            <select id="reportType">
                                <option value="sales">Sales Report</option>
                                <option value="inventory">Inventory Report</option>
                                <option value="customers">Customer Report</option>
                                <option value="financial">Financial Report</option>
                            </select>
                            <input type="date" id="startDate" value="<?php echo date('Y-m-01'); ?>">
                            <input type="date" id="endDate" value="<?php echo date('Y-m-d'); ?>">
                            <button class="btn btn-primary" id="generateReport">
                                <i class="fas fa-chart-line"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Revenue</h3>
                            <p class="stat-number">$0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Sales</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Products Sold</h3>
                            <p class="stat-number">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Growth Rate</h3>
                            <p class="stat-number">0%</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="content-section">
                    <h2>Sales Analytics</h2>
                    <div class="charts-grid">
                        <div class="chart-container">
                            <h3>Monthly Sales Trend</h3>
                            <div class="chart-placeholder">
                                <i class="fas fa-chart-line"></i>
                                <p>Sales trend chart will appear here</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <h3>Top Products</h3>
                            <div class="chart-placeholder">
                                <i class="fas fa-chart-bar"></i>
                                <p>Top products chart will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Reports Table -->
                <div class="content-section">
                    <h2>Detailed Report</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="no-data">
                                        <i class="fas fa-chart-bar"></i>
                                        <p>Select report type and date range to generate detailed report.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="content-section">
                    <div class="section-header">
                        <h2>Export Reports</h2>
                        <div class="export-buttons">
                            <button class="btn btn-outline">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                            <button class="btn btn-outline">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <button class="btn btn-outline">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                            <button class="btn btn-outline">
                                <i class="fas fa-print"></i> Print
                            </button>
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
        // Load reports on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.loadReports === 'function') {
                window.loadReports();
            }
        });
    </script>
</body>
</html>
