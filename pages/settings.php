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
            background: white;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .page-header h1 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .page-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: #2c3e50;
            color: white;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .settings-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
        }
        
        .settings-card h3 {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2c3e50;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }
    </style>
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

    <!-- Main Container with Sidebar -->
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
                <!-- Page Header -->
                <div class="page-header">
                    <h1>System Settings</h1>
                    <p>Configure your JIMS application preferences</p>
                </div>

                <!-- Settings Content -->
                <div class="settings-section">
                    <div class="settings-grid">
                        <!-- Business Settings -->
                        <div class="settings-card">
                            <h3><i class="fas fa-building"></i> Business Settings</h3>
                            <div class="form-group">
                                <label for="business-name">Business Name</label>
                                <input type="text" id="business-name" value="JIMS Jewellery Store" placeholder="Enter your business name">
                            </div>
                            <div class="form-group">
                                <label for="business-email">Business Email</label>
                                <input type="email" id="business-email" value="admin@jims.com" placeholder="Enter your business email">
                            </div>
                            <div class="form-group">
                                <label for="business-phone">Business Phone</label>
                                <input type="tel" id="business-phone" value="+1-800-JEWELRY" placeholder="Enter your business phone">
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="settings-card">
                            <h3><i class="fas fa-cog"></i> System Configuration</h3>
                            <div class="form-group">
                                <label for="currency">Default Currency</label>
                                <select id="currency">
                                    <option value="UGX" selected>UGX - Ugandan Shilling</option>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                    <option value="GBP">GBP - British Pound</option>
                                    <option value="KES">KES - Kenyan Shilling</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select id="timezone">
                                    <option value="UTC" selected>UTC</option>
                                    <option value="EST">Eastern Time</option>
                                    <option value="PST">Pacific Time</option>
                                    <option value="IST">India Standard Time</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="language">Language</label>
                                <select id="language">
                                    <option value="en" selected>English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="hi">Hindi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="settings-card">
                            <h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
                            <div class="form-group">
                                <label for="session-timeout">Session Timeout (minutes)</label>
                                <input type="number" id="session-timeout" value="30" min="5" max="480">
                            </div>
                            <div class="form-group">
                                <label for="password-policy">Password Policy</label>
                                <select id="password-policy">
                                    <option value="strong" selected>Strong (8+ chars, mixed case, numbers)</option>
                                    <option value="medium">Medium (6+ chars)</option>
                                    <option value="weak">Weak (4+ chars)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div style="text-align: center; margin-top: 30px;">
                        <button class="btn btn-primary" onclick="saveSettings()">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #2c3e50; color: white; padding: 20px 0; margin-top: auto;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin: 0; font-size: 0.9rem;">© 2026 JIMS - Jewellery Inventory Management System</p>
            </div>
        </div>
    </footer>
    <script>
        const SETTINGS_API = '../api/settings.php';

        document.addEventListener('DOMContentLoaded', loadSettings);

        async function loadSettings() {
            try {
                const res = await fetch(SETTINGS_API);
                const data = await res.json();

                if (data.businessName) document.getElementById('business-name').value = data.businessName;
                if (data.businessEmail) document.getElementById('business-email').value = data.businessEmail;
                if (data.businessPhone) document.getElementById('business-phone').value = data.businessPhone;
                if (data.currency) document.getElementById('currency').value = data.currency;
                if (data.timezone) document.getElementById('timezone').value = data.timezone;
                if (data.language) document.getElementById('language').value = data.language;
                if (data.sessionTimeout) document.getElementById('session-timeout').value = data.sessionTimeout;
                if (data.passwordPolicy) document.getElementById('password-policy').value = data.passwordPolicy;
            } catch (e) {
                console.error('Failed to load settings', e);
            }
        }

        async function saveSettings() {
            const payload = {
                businessName: document.getElementById('business-name').value,
                businessEmail: document.getElementById('business-email').value,
                businessPhone: document.getElementById('business-phone').value,
                currency: document.getElementById('currency').value,
                timezone: document.getElementById('timezone').value,
                language: document.getElementById('language').value,
                sessionTimeout: parseInt(document.getElementById('session-timeout').value, 10),
                passwordPolicy: document.getElementById('password-policy').value,
            };

            try {
                const res = await fetch(SETTINGS_API, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) {
                    alert('Settings saved successfully.');
                } else {
                    alert('Failed to save settings.');
                }
            } catch (e) {
                console.error('Failed to save settings', e);
                alert('Failed to save settings.');
            }
        }
    </script>
</body>
</html>
