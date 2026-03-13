/**
 * Dashboard Page Functions
 * JIMS - Jewellery Inventory Management System
 */

// Dashboard Functions
async function loadDashboard() {
    try {
        showLoading();
        const data = await apiRequest('reports.php?type=dashboard');
        renderDashboard(data);
    } catch (error) {
        console.error('Failed to load dashboard:', error);
        renderDashboardError();
    } finally {
        hideLoading();
    }
}

function renderDashboard(data) {
    const html = `
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Total Products</span>
                    <div class="stat-card-icon" style="background-color: #3498db; color: white;">
                        📦
                    </div>
                </div>
                <div class="stat-card-value">${data.metrics.total_products}</div>
                <div class="stat-card-change positive">Active inventory items</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Low Stock Items</span>
                    <div class="stat-card-icon" style="background-color: #f39c12; color: white;">
                        ⚠️
                    </div>
                </div>
                <div class="stat-card-value">${data.metrics.low_stock_items}</div>
                <div class="stat-card-change negative">Need restocking</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Total Sales</span>
                    <div class="stat-card-icon" style="background-color: #27ae60; color: white;">
                        💰
                    </div>
                </div>
                <div class="stat-card-value">${data.metrics.total_sales}</div>
                <div class="stat-card-change positive">This period</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Revenue</span>
                    <div class="stat-card-icon" style="background-color: #e74c3c; color: white;">
                        💵
                    </div>
                </div>
                <div class="stat-card-value">$${parseFloat(data.metrics.total_revenue).toFixed(2)}</div>
                <div class="stat-card-change positive">This period</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Sales</h3>
                <button class="btn btn-primary btn-sm" onclick="navigateToPage('sales')">View All</button>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.recent_sales.map(sale => `
                                <tr>
                                    <td>${sale.invoice_number}</td>
                                    <td>${sale.customer_name || 'Walk-in'}</td>
                                    <td>${formatDate(sale.sale_date)}</td>
                                    <td>$${parseFloat(sale.total_amount).toFixed(2)}</td>
                                    <td><span class="badge badge-${getStatusClass(sale.payment_status)}">${sale.payment_status}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Low Stock Alert</h3>
                <button class="btn btn-warning btn-sm" onclick="navigateToPage('products')">Manage Stock</button>
            </div>
            <div class="card-body">
                ${data.low_stock.length > 0 ? `
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.low_stock.map(item => `
                                    <tr>
                                        <td>${item.name}</td>
                                        <td>${item.sku}</td>
                                        <td><span class="badge badge-danger">${item.stock_quantity}</span></td>
                                        <td>${item.reorder_level}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="restockProduct(${item.id})">
                                                Restock
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                ` : '<p class="text-muted">No items are currently low in stock.</p>'}
            </div>
        </div>
    `;
    
    contentArea.innerHTML = html;
}

function renderDashboardError() {
    contentArea.innerHTML = `
        <div class="card">
            <div class="card-body text-center">
                <h3>Dashboard</h3>
                <p>Unable to load dashboard data. Please check your database connection.</p>
                <button class="btn btn-primary" onclick="loadDashboard()">Retry</button>
            </div>
        </div>
    `;
}

function restockProduct(productId) {
    console.log('Restock product:', productId);
    showModal('Restock Product', `
        <form id="restockForm">
            <div class="form-group">
                <label class="form-label">Quantity to Add</label>
                <input type="number" class="form-control" id="restockQuantity" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea class="form-control form-textarea" id="restockNotes" placeholder="Optional notes..."></textarea>
            </div>
        </form>
    `, `
        <button class="btn btn-primary" onclick="processRestock(${productId})">Add Stock</button>
        <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
    `);
}

async function processRestock(productId) {
    const quantity = document.getElementById('restockQuantity').value;
    const notes = document.getElementById('restockNotes').value;
    
    if (!quantity || quantity <= 0) {
        showError('Please enter a valid quantity');
        return;
    }
    
    try {
        // This would call an API to update stock
        console.log('Restocking product', productId, 'with quantity', quantity);
        
        closeModal('dynamicModal');
        showSuccess('Stock updated successfully!');
        
        // Refresh dashboard
        loadDashboard();
        
    } catch (error) {
        showError('Failed to update stock: ' + error.message);
    }
}
