/**
 * Sales Page Functions
 * JIMS - Jewellery Inventory Management System
 */

// Sales Functions
async function loadSales() {
    try {
        showLoading();
        const data = await apiRequest('sales.php');
        renderSales(data);
    } catch (error) {
        console.error('Failed to load sales:', error);
        renderSalesError();
    } finally {
        hideLoading();
    }
}

function renderSales(data) {
    const html = `
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales</h3>
                <div>
                    <button class="btn btn-primary" onclick="showAddSaleModal()">New Sale</button>
                </div>
            </div>
            <div class="card-body">
                <div class="search-bar">
                    <input type="text" class="form-control search-input" placeholder="Search sales..." id="saleSearch">
                    <input type="date" class="form-control" id="dateFrom">
                    <input type="date" class="form-control" id="dateTo">
                    <button class="btn btn-outline" onclick="searchSales()">Search</button>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.sales.map(sale => `
                                <tr>
                                    <td>${sale.invoice_number}</td>
                                    <td>${formatDate(sale.sale_date)}</td>
                                    <td>${sale.customer_name || 'Walk-in'}</td>
                                    <td>${sale.item_count}</td>
                                    <td>$${parseFloat(sale.total_amount).toFixed(2)}</td>
                                    <td>
                                        <span class="badge badge-${getStatusClass(sale.payment_status)}">
                                            ${sale.payment_status}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="btn btn-sm btn-primary" onclick="viewSale(${sale.id})">View</button>
                                            <button class="btn btn-sm btn-warning" onclick="editSale(${sale.id})">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteSale(${sale.id})">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                ${renderPagination(data.pagination)}
            </div>
        </div>
    `;
    
    contentArea.innerHTML = html;
}

function renderSalesError() {
    contentArea.innerHTML = `
        <div class="card">
            <div class="card-body text-center">
                <h3>Sales</h3>
                <p>Unable to load sales data. Please check your database connection.</p>
                <button class="btn btn-primary" onclick="loadSales()">Retry</button>
            </div>
        </div>
    `;
}

function showAddSaleModal() {
    showModal('New Sale', `
        <div id="saleProcess">
            <div class="sale-steps">
                <div class="step active" id="step1">
                    <h4>Step 1: Select Products</h4>
                    <div class="search-bar">
                        <input type="text" class="form-control" placeholder="Search products..." id="saleProductSearch">
                        <button class="btn btn-outline" onclick="searchSaleProducts()">Search</button>
                    </div>
                    <div id="saleProductsList"></div>
                </div>
                
                <div class="step" id="step2" style="display: none;">
                    <h4>Step 2: Customer Information</h4>
                    <div class="form-group">
                        <label class="form-label">Customer (Optional)</label>
                        <select class="form-select" id="saleCustomer">
                            <option value="">Walk-in Customer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" id="salePaymentMethod">
                            <option value="cash">Cash</option>
                            <option value="card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                </div>
                
                <div class="step" id="step3" style="display: none;">
                    <h4>Step 3: Review & Complete</h4>
                    <div id="saleSummary"></div>
                </div>
            </div>
            
            <div class="sale-cart">
                <h5>Cart</h5>
                <div id="saleCartItems">
                    <p class="text-muted">No items in cart</p>
                </div>
                <div class="sale-totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span id="saleSubtotal">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Tax:</span>
                        <span id="saleTax">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Total:</span>
                        <span id="saleTotal">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    `, `
        <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
        <button class="btn btn-primary" id="saleNextBtn" onclick="nextSaleStep()">Next</button>
        <button class="btn btn-success" id="saleCompleteBtn" style="display: none;" onclick="completeSale()">Complete Sale</button>
    `);
    
    loadSaleProducts();
    loadCustomersForSale();
}

let currentSaleStep = 1;
let saleCart = [];

function nextSaleStep() {
    if (currentSaleStep === 1 && saleCart.length === 0) {
        showError('Please add at least one item to the cart');
        return;
    }
    
    document.getElementById(`step${currentSaleStep}`).style.display = 'none';
    currentSaleStep++;
    document.getElementById(`step${currentSaleStep}`).style.display = 'block';
    
    if (currentSaleStep === 3) {
        showSaleSummary();
        document.getElementById('saleNextBtn').style.display = 'none';
        document.getElementById('saleCompleteBtn').style.display = 'inline-block';
    }
    
    updateSaleTotals();
}

function showSaleSummary() {
    const summaryHTML = `
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${saleCart.map(item => `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.quantity}</td>
                            <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td>$${(item.quantity * item.unit_price).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        
        <div class="sale-summary-details">
            <p><strong>Customer:</strong> ${document.getElementById('saleCustomer').options[document.getElementById('saleCustomer').selectedIndex].text}</p>
            <p><strong>Payment Method:</strong> ${document.getElementById('salePaymentMethod').value}</p>
            <p><strong>Subtotal:</strong> $${calculateSaleSubtotal().toFixed(2)}</p>
            <p><strong>Tax:</strong> $${calculateSaleTax().toFixed(2)}</p>
            <p><strong>Total:</strong> $${calculateSaleTotal().toFixed(2)}</p>
        </div>
    `;
    
    document.getElementById('saleSummary').innerHTML = summaryHTML;
}

async function loadSaleProducts() {
    try {
        const data = await apiRequest('products.php');
        renderSaleProducts(data.products);
    } catch (error) {
        console.error('Failed to load products for sale:', error);
    }
}

function renderSaleProducts(products) {
    const container = document.getElementById('saleProductsList');
    const html = `
        <div class="products-grid">
            ${products.map(product => `
                <div class="product-card">
                    <h6>${product.name}</h6>
                    <p class="text-muted">${product.sku}</p>
                    <p class="price">$${parseFloat(product.selling_price).toFixed(2)}</p>
                    <p class="stock">Stock: ${product.stock_quantity}</p>
                    <div class="add-to-cart">
                        <input type="number" class="form-control form-control-sm" id="qty-${product.id}" min="1" max="${product.stock_quantity}" value="1">
                        <button class="btn btn-sm btn-primary" onclick="addToCart(${product.id})">Add</button>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    container.innerHTML = html;
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById(`qty-${productId}`).value);
    
    // Find product in the list (this is simplified - in production, you'd have the product data)
    apiRequest(`products.php?id=${productId}`)
        .then(product => {
            if (quantity > product.stock_quantity) {
                showError('Insufficient stock');
                return;
            }
            
            // Check if already in cart
            const existingItem = saleCart.find(item => item.product_id === productId);
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                saleCart.push({
                    product_id: product.id,
                    name: product.name,
                    sku: product.sku,
                    unit_price: product.selling_price,
                    quantity: quantity
                });
            }
            
            updateSaleCart();
            showSuccess('Added to cart');
        })
        .catch(error => {
            showError('Failed to add to cart: ' + error.message);
        });
}

function updateSaleCart() {
    const cartContainer = document.getElementById('saleCartItems');
    
    if (saleCart.length === 0) {
        cartContainer.innerHTML = '<p class="text-muted">No items in cart</p>';
    } else {
        const html = saleCart.map(item => `
            <div class="cart-item">
                <div class="cart-item-info">
                    <strong>${item.name}</strong><br>
                    <small>${item.sku} - $${parseFloat(item.unit_price).toFixed(2)} x ${item.quantity}</small>
                </div>
                <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.product_id})">×</button>
            </div>
        `).join('');
        
        cartContainer.innerHTML = html;
    }
    
    updateSaleTotals();
}

function removeFromCart(productId) {
    saleCart = saleCart.filter(item => item.product_id !== productId);
    updateSaleCart();
}

function calculateSaleSubtotal() {
    return saleCart.reduce((total, item) => total + (item.quantity * item.unit_price), 0);
}

function calculateSaleTax() {
    return calculateSaleSubtotal() * 0.00; // 0% tax by default
}

function calculateSaleTotal() {
    return calculateSaleSubtotal() + calculateSaleTax();
}

function updateSaleTotals() {
    document.getElementById('saleSubtotal').textContent = `$${calculateSaleSubtotal().toFixed(2)}`;
    document.getElementById('saleTax').textContent = `$${calculateSaleTax().toFixed(2)}`;
    document.getElementById('saleTotal').textContent = `$${calculateSaleTotal().toFixed(2)}`;
}

async function completeSale() {
    const saleData = {
        customer_id: document.getElementById('saleCustomer').value || null,
        payment_method: document.getElementById('salePaymentMethod').value,
        items: saleCart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            unit_price: item.unit_price
        }))
    };
    
    try {
        await apiRequest('sales.php', {
            method: 'POST',
            body: saleData
        });
        
        closeModal('dynamicModal');
        showSuccess('Sale completed successfully!');
        
        // Reset cart and reload sales
        saleCart = [];
        currentSaleStep = 1;
        loadSales();
        
    } catch (error) {
        showError('Failed to complete sale: ' + error.message);
    }
}

function viewSale(saleId) {
    apiRequest(`sales.php?id=${saleId}`)
        .then(sale => {
            const itemsHTML = sale.items.map(item => `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${item.sku}</td>
                    <td>${item.quantity}</td>
                    <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td>$${parseFloat(item.line_total).toFixed(2)}</td>
                </tr>
            `).join('');
            
            showModal(`Sale Details - ${sale.invoice_number}`, `
                <div class="sale-details">
                    <div class="sale-info">
                        <p><strong>Invoice:</strong> ${sale.invoice_number}</p>
                        <p><strong>Date:</strong> ${formatDate(sale.sale_date)}</p>
                        <p><strong>Customer:</strong> ${sale.customer_name || 'Walk-in'}</p>
                        <p><strong>Payment Method:</strong> ${sale.payment_method}</p>
                        <p><strong>Status:</strong> <span class="badge badge-${getStatusClass(sale.payment_status)}">${sale.payment_status}</span></p>
                    </div>
                    
                    <h5>Items</h5>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHTML}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="sale-totals-summary">
                        <p><strong>Subtotal:</strong> $${parseFloat(sale.subtotal).toFixed(2)}</p>
                        <p><strong>Tax:</strong> $${parseFloat(sale.tax_amount).toFixed(2)}</p>
                        <p><strong>Total:</strong> $${parseFloat(sale.total_amount).toFixed(2)}</p>
                    </div>
                </div>
            `);
        })
        .catch(error => {
            showError('Failed to load sale details: ' + error.message);
        });
}

function editSale(saleId) {
    console.log('Edit sale:', saleId);
    // Implementation for editing sales
    showSuccess('Edit sale functionality will be implemented');
}

function deleteSale(saleId) {
    if (confirm('Are you sure you want to delete this sale? This will restore the items to inventory.')) {
        apiRequest(`sales.php?id=${saleId}`, { method: 'DELETE' })
            .then(() => {
                showSuccess('Sale deleted successfully!');
                loadSales();
            })
            .catch(error => {
                showError('Failed to delete sale: ' + error.message);
            });
    }
}

function searchSales() {
    const searchTerm = document.getElementById('saleSearch').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    // Build query parameters
    const params = new URLSearchParams();
    if (searchTerm) params.append('search', searchTerm);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    
    const url = `sales.php${params.toString() ? '?' + params.toString() : ''}`;
    
    apiRequest(url)
        .then(data => {
            renderSales(data);
        })
        .catch(error => {
            showError('Failed to search sales: ' + error.message);
        });
}

function searchSaleProducts() {
    const searchTerm = document.getElementById('saleProductSearch').value;
    
    if (searchTerm) {
        apiRequest(`products.php?search=${searchTerm}`)
            .then(data => {
                renderSaleProducts(data.products);
            })
            .catch(error => {
                showError('Failed to search products: ' + error.message);
            });
    } else {
        loadSaleProducts();
    }
}

async function loadCustomersForSale() {
    try {
        const data = await apiRequest('api/customers.php');
        const customerSelect = document.getElementById('saleCustomer');
        
        if (customerSelect && data.customers) {
            data.customers.forEach(customer => {
                const option = document.createElement('option');
                option.value = customer.id;
                option.textContent = customer.name;
                customerSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Failed to load customers for sale:', error);
    }
}
