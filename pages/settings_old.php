<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - JIMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="JIMS Settings - System Configuration">
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
                            <a href="reports.php" class="sidebar-link" data-page="reports">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="settings.php" class="sidebar-link active" data-page="settings">
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
                    <h1>Settings</h1>
                    <p>System configuration and preferences</p>
                </div>

                <!-- Settings Tabs -->
                <div class="settings-tabs">
                    <button class="tab-btn active" data-tab="general">
                        <i class="fas fa-cog"></i> General
                    </button>
                    <button class="tab-btn" data-tab="users">
                        <i class="fas fa-users"></i> Users
                    </button>
                    <button class="tab-btn" data-tab="store">
                        <i class="fas fa-store"></i> Store
                    </button>
                    <button class="tab-btn" data-tab="backup">
                        <i class="fas fa-database"></i> Backup
                    </button>
                </div>

                <!-- General Settings -->
                <div class="tab-content active" id="general">
                    <div class="content-section">
                        <h2>General Settings</h2>
                        <form class="settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="storeName">Store Name</label>
                                    <input type="text" id="storeName" value="JIMS Jewellery Store">
                                </div>
                                <div class="form-group">
                                    <label for="storeEmail">Store Email</label>
                                    <input type="email" id="storeEmail" value="info@jims.com">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="storePhone">Store Phone</label>
                                    <input type="tel" id="storePhone" value="+1234567890">
                                </div>
                                <div class="form-group">
                                    <label for="storeAddress">Store Address</label>
                                    <input type="text" id="storeAddress" value="123 Main St, City, Country">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select id="currency">
                                    <option value="USD" selected>USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="taxRate">Tax Rate (%)</label>
                                <input type="number" id="taxRate" value="10" step="0.1">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>

                <!-- User Management -->
                <div class="tab-content" id="users">
                    <div class="content-section">
                        <div class="section-header">
                            <h2>User Management</h2>
                            <button class="btn btn-primary" id="addUserBtn">
                                <i class="fas fa-plus"></i> Add User
                            </button>
                        </div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>admin</td>
                                        <td>Administrator</td>
                                        <td><span class="role-badge admin">Admin</span></td>
                                        <td><span class="status-badge active">Active</span></td>
                                        <td>Never</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline">Edit</button>
                                            <button class="btn btn-sm btn-outline">Reset Password</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Store Settings -->
                <div class="tab-content" id="store">
                    <div class="content-section">
                        <h2>Store Configuration</h2>
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="businessHours">Business Hours</label>
                                <input type="text" id="businessHours" value="9:00 AM - 6:00 PM">
                            </div>
                            <div class="form-group">
                                <label for="logoUpload">Store Logo</label>
                                <div class="file-upload">
                                    <input type="file" id="logoUpload" accept="image/*">
                                    <label for="logoUpload" class="file-label">
                                        <i class="fas fa-upload"></i> Choose Logo
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" checked> Enable Email Notifications
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" checked> Enable Low Stock Alerts
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>

                <!-- Backup Settings -->
                <div class="tab-content" id="backup">
                    <div class="content-section">
                        <h2>Backup & Restore</h2>
                        <div class="backup-section">
                            <div class="backup-info">
                                <h3>Last Backup</h3>
                                <p>No backup performed yet</p>
                            </div>
                            <div class="backup-actions">
                                <button class="btn btn-primary" id="backupBtn">
                                    <i class="fas fa-download"></i> Create Backup
                                </button>
                                <button class="btn btn-outline" id="restoreBtn">
                                    <i class="fas fa-upload"></i> Restore Backup
                                </button>
                            </div>
                        </div>
                        <div class="backup-schedule">
                            <h3>Automatic Backup</h3>
                            <form class="settings-form">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox"> Enable Daily Backups
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="backupTime">Backup Time</label>
                                    <input type="time" id="backupTime" value="02:00">
                                </div>
                                <div class="form-group">
                                    <label for="backupRetention">Retention Period (days)</label>
                                    <input type="number" id="backupRetention" value="30">
                                </div>
                            </form>
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
        // Load settings on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.loadSettings === 'function') {
                window.loadSettings();
            }
            
            // Tab switching
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tabId = btn.dataset.tab;
                    
                    // Remove active class from all tabs and contents
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    btn.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
