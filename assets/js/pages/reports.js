/**
 * Reports Page Functions
 * JIMS - Jewellery Inventory Management System
 */

// Reports Functions
async function loadReports() {
    try {
        showLoading();
        
        // Get dashboard data for overview
        const dashboardData = await apiRequest('reports.php?type=dashboard');
        
        renderReports(dashboardData);
    } catch (error) {
        console.error('Failed to load reports:', error);
        renderReportsError();
    } finally {
        hideLoading();
    }
}

function renderReports(data) {
    const html = `
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reports & Analytics</h3>
                <div>
                    <select class="form-select" id="reportPeriod" onchange="updateReportPeriod()">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportReport()">Export</button>
                </div>
            </div>
            <div class="card-body">
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <span class="stat-card-title">Total Revenue</span>
                            <div class="stat-card-icon" style="background-color: #27ae60; color: white;">
                                💵
                            </div>
                        </div>
                        <div class="stat-card-value">$${parseFloat(data.metrics.total_revenue || 0).toFixed(2)}</div>
                        <div class="stat-card-change positive">This period</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <span class="stat-card-title">Total Sales</span>
                            <div class="stat-card-icon" style="background-color: #3498db; color: white;">
                                📊
                            </div>
                        </div>
                        <div class="stat-card-value">${data.metrics.total_sales || 0}</div>
                        <div class="stat-card-change positive">Transactions</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <span class="stat-card-title">Average Sale</span>
                            <div class="stat-card-icon" style="background-color: #f39c12; color: white;">
                                📈
                            </div>
                        </div>
                        <div class="stat-card-value">$${parseFloat(data.metrics.avg_sale_value || 0).toFixed(2)}</div>
                        <div class="stat-card-change positive">Per transaction</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <span class="stat-card-title">Products Sold</span>
                            <div class="stat-card-icon" style="background-color: #e74c3c; color: white;">
                                📦
                            </div>
                        </div>
                        <div class="stat-card-value">${data.top_products ? data.top_products.reduce((sum, p) => sum + (p.total_sold || 0), 0) : 0}</div>
                        <div class="stat-card-change positive">Items sold</div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Sales Trend</h4>
                        <button class="btn btn-sm btn-outline" onclick="refreshSalesTrend()">Refresh</button>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" style="max-height: 400px;"></canvas>
                        <div id="salesChartPlaceholder" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                            <div class="text-center">
                                <i class="fas fa-chart-line" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Sales trend chart will be displayed here</p>
                                <small>Chart library integration needed for visualization</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="card">
                        <div class="card-header">
                            <h4>Top Selling Products</h4>
                        </div>
                        <div class="card-body">
                            ${data.top_products && data.top_products.length > 0 ? `
                                <div class="table-container">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>SKU</th>
                                                <th>Units Sold</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.top_products.slice(0, 5).map(product => `
                                                <tr>
                                                    <td>${product.name}</td>
                                                    <td>${product.sku}</td>
                                                    <td>${product.total_sold || 0}</td>
                                                    <td>$${parseFloat((product.total_sold || 0) * (product.selling_price || 0)).toFixed(2)}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            ` : '<p class="text-muted">No sales data available</p>'}
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h4>Low Stock Alerts</h4>
                        </div>
                        <div class="card-body">
                            ${data.low_stock && data.low_stock.length > 0 ? `
                                <div class="table-container">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Current Stock</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.low_stock.slice(0, 5).map(item => `
                                                <tr>
                                                    <td>${item.name}</td>
                                                    <td><span class="badge badge-danger">${item.stock_quantity}</span></td>
                                                    <td><span class="badge badge-warning">Low Stock</span></td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            ` : '<p class="text-muted">No low stock items</p>'}
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Report Options</h4>
                    </div>
                    <div class="card-body">
                        <div class="report-options">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Report Type</label>
                                    <select class="form-select" id="reportType">
                                        <option value="sales">Sales Report</option>
                                        <option value="inventory">Inventory Report</option>
                                        <option value="profit">Profit Analysis</option>
                                        <option value="suppliers">Supplier Report</option>
                                        <option value="customers">Customer Analytics</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date Range</label>
                                    <select class="form-select" id="dateRange">
                                        <option value="today">Today</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                        <option value="quarter">This Quarter</option>
                                        <option value="year">This Year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row" id="customDateRange" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="fromDate">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="toDate">
                                </div>
                            </div>
                            <button class="btn btn-primary" onclick="generateCustomReport()">Generate Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    contentArea.innerHTML = html;
    
    // Initialize chart placeholder
    setTimeout(() => {
        const chartElement = document.getElementById('salesChart');
        const placeholder = document.getElementById('salesChartPlaceholder');
        if (chartElement && placeholder) {
            placeholder.style.display = 'flex';
        }
    }, 100);
    
    // Setup date range change handler
    setupDateRangeHandler();
}

function renderReportsError() {
    contentArea.innerHTML = `
        <div class="card">
            <div class="card-body text-center">
                <h3>Reports</h3>
                <p>Unable to load reports data. Please check your database connection.</p>
                <button class="btn btn-primary" onclick="loadReports()">Retry</button>
            </div>
        </div>
    `;
}

function setupDateRangeHandler() {
    const dateRangeSelect = document.getElementById('dateRange');
    const customDateRange = document.getElementById('customDateRange');
    
    if (dateRangeSelect) {
        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = 'grid';
            } else {
                customDateRange.style.display = 'none';
            }
        });
    }
}

function updateReportPeriod() {
    const period = document.getElementById('reportPeriod').value;
    console.log('Report period changed to:', period);
    // Reload reports with new period
    loadReports();
}

function exportReport() {
    console.log('Exporting report...');
    showSuccess('Report export functionality will be implemented');
}

function refreshSalesTrend() {
    console.log('Refreshing sales trend...');
    loadReports();
}

async function generateCustomReport() {
    const reportType = document.getElementById('reportType').value;
    const dateRange = document.getElementById('dateRange').value;
    
    let dateFrom, dateTo;
    
    // Calculate date range
    const today = new Date();
    switch (dateRange) {
        case 'today':
            dateFrom = dateTo = today.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            dateFrom = weekStart.toISOString().split('T')[0];
            dateTo = today.toISOString().split('T')[0];
            break;
        case 'month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            dateFrom = monthStart.toISOString().split('T')[0];
            dateTo = today.toISOString().split('T')[0];
            break;
        case 'quarter':
            const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
            dateFrom = quarterStart.toISOString().split('T')[0];
            dateTo = today.toISOString().split('T')[0];
            break;
        case 'year':
            const yearStart = new Date(today.getFullYear(), 0, 1);
            dateFrom = yearStart.toISOString().split('T')[0];
            dateTo = today.toISOString().split('T')[0];
            break;
        case 'custom':
            dateFrom = document.getElementById('fromDate').value;
            dateTo = document.getElementById('toDate').value;
            if (!dateFrom || !dateTo) {
                showError('Please select both from and to dates');
                return;
            }
            break;
    }
    
    try {
        showLoading();
        
        // Build query parameters
        const params = new URLSearchParams();
        params.append('type', reportType);
        params.append('date_from', dateFrom);
        params.append('date_to', dateTo);
        
        const reportData = await apiRequest(`reports.php?${params.toString()}`);
        
        // Display the custom report
        displayCustomReport(reportType, reportData, dateFrom, dateTo);
        
    } catch (error) {
        showError('Failed to generate report: ' + error.message);
    } finally {
        hideLoading();
    }
}

function displayCustomReport(reportType, data, dateFrom, dateTo) {
    let reportHTML = '';
    
    switch (reportType) {
        case 'sales':
            reportHTML = renderSalesReport(data, dateFrom, dateTo);
            break;
        case 'inventory':
            reportHTML = renderInventoryReport(data, dateFrom, dateTo);
            break;
        case 'profit':
            reportHTML = renderProfitReport(data, dateFrom, dateTo);
            break;
        case 'suppliers':
            reportHTML = renderSupplierReport(data, dateFrom, dateTo);
            break;
        case 'customers':
            reportHTML = renderCustomerReport(data, dateFrom, dateTo);
            break;
        default:
            reportHTML = '<p>Report type not implemented</p>';
    }
    
    showModal(`Custom Report - ${reportType.charAt(0).toUpperCase() + reportType.slice(1)}`, `
        <div class="custom-report">
            <div class="report-header">
                <p><strong>Period:</strong> ${formatDate(dateFrom)} to ${formatDate(dateTo)}</p>
                <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
            </div>
            ${reportHTML}
        </div>
    `, `
        <button class="btn btn-primary" onclick="exportCustomReport('${reportType}', '${dateFrom}', '${dateTo}')">Export</button>
        <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Close</button>
    `);
}

function renderSalesReport(data, dateFrom, dateTo) {
    if (!data.sales_trend || data.sales_trend.length === 0) {
        return '<p>No sales data available for the selected period.</p>';
    }
    
    const salesRows = data.sales_trend.map(period => `
        <tr>
            <td>${period.period}</td>
            <td>${period.total_sales}</td>
            <td>$${parseFloat(period.total_revenue).toFixed(2)}</td>
            <td>$${parseFloat(period.avg_sale_value).toFixed(2)}</td>
            <td>${period.unique_customers}</td>
        </tr>
    `).join('');
    
    return `
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Sales Count</th>
                        <th>Revenue</th>
                        <th>Avg Sale Value</th>
                        <th>Unique Customers</th>
                    </tr>
                </thead>
                <tbody>
                    ${salesRows}
                </tbody>
            </table>
        </div>
    `;
}

function renderInventoryReport(data, dateFrom, dateTo) {
    return `
        <div class="inventory-summary">
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-value">${data.summary?.total_products || 0}</div>
                    <div class="stat-card-title">Total Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">${data.summary?.total_items || 0}</div>
                    <div class="stat-card-title">Total Items in Stock</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">$${parseFloat(data.summary?.total_cost_value || 0).toFixed(2)}</div>
                    <div class="stat-card-title">Total Cost Value</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">$${parseFloat(data.summary?.total_sell_value || 0).toFixed(2)}</div>
                    <div class="stat-card-title">Total Sell Value</div>
                </div>
            </div>
        </div>
    `;
}

function renderProfitReport(data, dateFrom, dateTo) {
    if (!data.profit_trend || data.profit_trend.length === 0) {
        return '<p>No profit data available for the selected period.</p>';
    }
    
    const profitRows = data.profit_trend.map(period => `
        <tr>
            <td>${period.period}</td>
            <td>$${parseFloat(period.revenue).toFixed(2)}</td>
            <td>$${parseFloat(period.cost_of_goods_sold).toFixed(2)}</td>
            <td>$${parseFloat(period.gross_profit).toFixed(2)}</td>
            <td>${parseFloat(period.profit_margin).toFixed(2)}%</td>
        </tr>
    `).join('');
    
    return `
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Revenue</th>
                        <th>Cost of Goods Sold</th>
                        <th>Gross Profit</th>
                        <th>Profit Margin</th>
                    </tr>
                </thead>
                <tbody>
                    ${profitRows}
                </tbody>
            </table>
        </div>
    `;
}

function renderSupplierReport(data, dateFrom, dateTo) {
    if (!data.supplier_performance || data.supplier_performance.length === 0) {
        return '<p>No supplier data available for the selected period.</p>';
    }
    
    const supplierRows = data.supplier_performance.map(supplier => `
        <tr>
            <td>${supplier.name}</td>
            <td>${supplier.product_count}</td>
            <td>${supplier.purchase_order_count}</td>
            <td>$${parseFloat(supplier.total_purchase_value).toFixed(2)}</td>
            <td>$${parseFloat(supplier.avg_order_value).toFixed(2)}</td>
        </tr>
    `).join('');
    
    return `
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Products</th>
                        <th>Purchase Orders</th>
                        <th>Total Purchase Value</th>
                        <th>Avg Order Value</th>
                    </tr>
                </thead>
                <tbody>
                    ${supplierRows}
                </tbody>
            </table>
        </div>
    `;
}

function renderCustomerReport(data, dateFrom, dateTo) {
    if (!data.top_customers || data.top_customers.length === 0) {
        return '<p>No customer data available for the selected period.</p>';
    }
    
    const customerRows = data.top_customers.map(customer => `
        <tr>
            <td>${customer.name}</td>
            <td>${customer.purchase_count}</td>
            <td>$${parseFloat(customer.total_spent).toFixed(2)}</td>
            <td>$${parseFloat(customer.avg_purchase).toFixed(2)}</td>
            <td>${formatDate(customer.last_purchase_date)}</td>
        </tr>
    `).join('');
    
    return `
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Purchase Count</th>
                        <th>Total Spent</th>
                        <th>Avg Purchase</th>
                        <th>Last Purchase</th>
                    </tr>
                </thead>
                <tbody>
                    ${customerRows}
                </tbody>
            </table>
        </div>
    `;
}

function exportCustomReport(reportType, dateFrom, dateTo) {
    console.log(`Exporting ${reportType} report from ${dateFrom} to ${dateTo}`);
    showSuccess('Report export functionality will be implemented');
}
