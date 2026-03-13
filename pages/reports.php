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
            background: #f3f4f6;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .page-header-title h1 {
            font-size: 1.8rem;
            color: #111827;
            margin: 0 0 6px 0;
        }
        
        .page-header-title p {
            color: #6b7280;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .page-header-meta {
            text-align: right;
            font-size: 0.85rem;
            color: #6b7280;
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
            background: #111827;
            color: white;
        }

        .reports-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .reports-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .reports-header-text h2 {
            font-size: 1.3rem;
            margin: 0 0 6px 0;
            color: #111827;
        }

        .reports-header-text p {
            margin: 0;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .metric-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-top: 4px;
        }

        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        }

        .metric-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .metric-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #111827;
        }

        .metric-sub {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-top: 2px;
        }
        
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 16px 18px 6px 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
        }

        .table-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .table-card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #111827;
        }

        .table-card-subtitle {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 4px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .data-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 1px solid #e9ecef;
        }
        
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .data-table tr:hover {
            background: #f9fafb;
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
                            <a href="reports.php" class="sidebar-link active" data-page="reports">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="users.php" class="sidebar-link" data-page="users">
                                <i class="fas fa-user-shield"></i>
                                <span>Users</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="main-area">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-header-title">
                        <h1>Reports & Analytics</h1>
                        <p>Track sales performance and key trends over time.</p>
                    </div>
                    <div class="page-header-meta">
                        <div id="report-period-label">Current year overview</div>
                        <div id="report-updated-label">Last updated: just now</div>
                    </div>
                </div>

                <!-- Reports Content -->
                <div class="reports-section">
                    <div class="reports-header-row">
                        <div class="reports-header-text">
                            <h2>Sales Overview</h2>
                            <p>Headline metrics for the selected period.</p>
                        </div>
                        <!-- Report Filters -->
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="report-period">Period</label>
                                <select id="report-period">
                                    <option value="monthly" selected>Monthly (this year)</option>
                                    <option value="yearly">Last 5 years</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" onclick="loadReports()">
                                <i class="fas fa-sync"></i> Refresh
                            </button>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>

                    <!-- Metrics summary -->
                    <div class="metric-cards">
                        <div class="metric-card">
                            <div class="metric-label">Total Sales</div>
                            <div class="metric-value" id="metric-total-sales">0</div>
                            <div class="metric-sub">Number of invoices</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Total Revenue</div>
                            <div class="metric-value" id="metric-total-revenue">UGX 0</div>
                            <div class="metric-sub">Gross revenue for period</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Average Sale Value</div>
                            <div class="metric-value" id="metric-avg-sale">UGX 0</div>
                            <div class="metric-sub">Per completed sale</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Best Period</div>
                            <div class="metric-value" id="metric-best-period">–</div>
                            <div class="metric-sub">Highest revenue period</div>
                        </div>
                    </div>

                    <!-- Reports Table -->
                    <div class="table-card">
                        <div class="table-card-header">
                            <div class="table-card-title">Sales breakdown</div>
                            <div class="table-card-subtitle" id="table-subtitle">By month for current year</div>
                        </div>
                        <div class="table-container">
                            <table class="data-table" id="reportsTable">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Total Sales</th>
                                        <th>Revenue</th>
                                        <th>Gross Revenue</th>
                                        <th>Avg Sale Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px; color: #6c757d;">
                                            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 15px; display: block;"></i>
                                            <p>Loading report data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                <p style="margin: 0; font-size: 0.9rem;">© 2026 JIMS - Jewellery Inventory Management System</p>
            </div>
        </div>
    </footer>

    <script>
        const REPORTS_API = '../api/reports.php?type=sales';

        document.addEventListener('DOMContentLoaded', loadReports);

        async function loadReports() {
            const period = document.getElementById('report-period').value || 'monthly';
            const year = new Date().getFullYear();
            const url = `${REPORTS_API}&period=${encodeURIComponent(period)}&year=${year}`;
            
            const tbody = document.querySelector('#reportsTable tbody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align:center; padding: 30px; color:#6c757d;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 10px; display:block;"></i>
                        Loading report data...
                    </td>
                </tr>`;

            // Update header labels
            const periodLabel = document.getElementById('report-period-label');
            const tableSubtitle = document.getElementById('table-subtitle');
            const updatedLabel = document.getElementById('report-updated-label');
            periodLabel.textContent = period === 'monthly' ? 'Current year overview' : 'Last 5 years overview';
            tableSubtitle.textContent = period === 'monthly' ? 'By month for current year' : 'By year for last 5 years';
            updatedLabel.textContent = 'Last updated: ' + new Date().toLocaleString();

            try {
                const res = await fetch(url);
                const data = await res.json();
                const rows = data.sales_trend || [];

                if (!rows.length) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px; color:#6c757d;">
                                <i class="fas fa-chart-bar" style="font-size: 2rem; margin-bottom: 10px; display:block;"></i>
                                No report data available for the selected period.
                            </td>
                        </tr>`;
                    return;
                }

                // Update metrics from rows
                let totalSales = 0;
                let totalRevenue = 0;
                let totalAvg = 0;
                let bestPeriod = '-';
                let bestRevenue = 0;

                rows.forEach(r => {
                    const s = Number(r.total_sales || 0);
                    const rev = Number(r.total_revenue || 0);
                    const avg = Number(r.avg_sale_value || 0);
                    totalSales += s;
                    totalRevenue += rev;
                    totalAvg += avg;
                    if (rev > bestRevenue) {
                        bestRevenue = rev;
                        bestPeriod = r.period;
                    }
                });

                const metricTotalSales = document.getElementById('metric-total-sales');
                const metricTotalRevenue = document.getElementById('metric-total-revenue');
                const metricAvgSale = document.getElementById('metric-avg-sale');
                const metricBestPeriod = document.getElementById('metric-best-period');

                metricTotalSales.textContent = totalSales.toLocaleString();
                metricTotalRevenue.textContent = 'UGX ' + totalRevenue.toLocaleString();
                const avgOverall = rows.length ? totalRevenue / Math.max(totalSales, 1) : 0;
                metricAvgSale.textContent = 'UGX ' + Math.round(avgOverall).toLocaleString();
                metricBestPeriod.textContent = bestRevenue > 0 ? `${bestPeriod}` : '–';

                tbody.innerHTML = rows.map(r => `
                    <tr>
                        <td>${r.period}</td>
                        <td>${Number(r.total_sales || 0).toLocaleString()}</td>
                        <td>UGX ${Number(r.total_revenue || 0).toLocaleString()}</td>
                        <td>UGX ${Number(r.gross_revenue || 0).toLocaleString()}</td>
                        <td>UGX ${Number(r.avg_sale_value || 0).toFixed(0).toLocaleString()}</td>
                    </tr>
                `).join('');
            } catch (e) {
                console.error('Error loading reports:', e);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 30px; color:#dc3545;">
                            Failed to load report data. Please try again.
                        </td>
                    </tr>`;
            }
        }
    </script>
</body>
</html>
