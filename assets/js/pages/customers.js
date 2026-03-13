/**
 * Customers Page Functions
 * JIMS - Jewellery Inventory Management System
 */

// Customers Functions
async function loadCustomers() {
    try {
        showLoading();
        const data = await apiRequest('customers.php');
        renderCustomers(data);
    } catch (error) {
        console.error('Failed to load customers:', error);
        renderCustomersError();
    } finally {
        hideLoading();
    }
}

function renderCustomers(data) {
    const html = `
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Customers</h3>
                <div>
                    <button class="btn btn-primary" onclick="showAddCustomerModal()">Add Customer</button>
                </div>
            </div>
            <div class="card-body">
                <div class="search-bar">
                    <input type="text" class="form-control search-input" placeholder="Search customers..." id="customerSearch">
                    <button class="btn btn-outline" onclick="searchCustomers()">Search</button>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Purchases</th>
                                <th>Total Spent</th>
                                <th>Last Purchase</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.customers.map(customer => `
                                <tr>
                                    <td>${customer.name}</td>
                                    <td>${customer.email || 'N/A'}</td>
                                    <td>${customer.phone || 'N/A'}</td>
                                    <td>${customer.purchase_count || 0}</td>
                                    <td>$${parseFloat(customer.total_spent || 0).toFixed(2)}</td>
                                    <td>${customer.last_purchase_date ? formatDate(customer.last_purchase_date) : 'Never'}</td>
                                    <td>
                                        <span class="badge badge-${customer.is_active ? 'success' : 'secondary'}">
                                            ${customer.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="btn btn-sm btn-primary" onclick="viewCustomer(${customer.id})">View</button>
                                            <button class="btn btn-sm btn-warning" onclick="editCustomer(${customer.id})">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${customer.id})">Delete</button>
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

function renderCustomersError() {
    contentArea.innerHTML = `
        <div class="card">
            <div class="card-body text-center">
                <h3>Customers</h3>
                <p>Unable to load customers data. Please check your database connection.</p>
                <button class="btn btn-primary" onclick="loadCustomers()">Retry</button>
            </div>
        </div>
    `;
}

function showAddCustomerModal() {
    showModal('Add Customer', `
        <form id="customerForm">
            <div class="form-group">
                <label class="form-label">Customer Name *</label>
                <input type="text" class="form-control" id="customerName" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="customerEmail">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="customerPhone">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea class="form-control form-textarea" id="customerAddress"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" id="customerCity">
                </div>
                <div class="form-group">
                    <label class="form-label">State/Province</label>
                    <input type="text" class="form-control" id="customerState">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Postal Code</label>
                    <input type="text" class="form-control" id="customerPostalCode">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" class="form-control" id="customerCountry">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Birth Date</label>
                    <input type="date" class="form-control" id="customerBirthDate">
                </div>
                <div class="form-group">
                    <label class="form-label">Anniversary Date</label>
                    <input type="date" class="form-control" id="customerAnniversary">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Preferences</label>
                <textarea class="form-control form-textarea" id="customerPreferences" placeholder="Customer preferences, notes, etc."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea class="form-control form-textarea" id="customerNotes" placeholder="Additional notes..."></textarea>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="customerActive" checked>
                    <label class="form-check-label" for="customerActive">Active</label>
                </div>
            </div>
        </form>
    `, `
        <button class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
        <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
    `);
}

async function saveCustomer() {
    const customerData = {
        name: document.getElementById('customerName').value,
        email: document.getElementById('customerEmail').value,
        phone: document.getElementById('customerPhone').value,
        address: document.getElementById('customerAddress').value,
        city: document.getElementById('customerCity').value,
        state: document.getElementById('customerState').value,
        postal_code: document.getElementById('customerPostalCode').value,
        country: document.getElementById('customerCountry').value,
        birth_date: document.getElementById('customerBirthDate').value,
        anniversary_date: document.getElementById('customerAnniversary').value,
        preferences: document.getElementById('customerPreferences').value,
        notes: document.getElementById('customerNotes').value,
        is_active: document.getElementById('customerActive').checked
    };
    
    // Validate required fields
    if (!customerData.name) {
        showError('Customer name is required');
        return;
    }
    
    try {
        await apiRequest('customers.php', {
            method: 'POST',
            body: customerData
        });
        
        closeModal('dynamicModal');
        showSuccess('Customer added successfully!');
        loadCustomers();
        
    } catch (error) {
        showError('Failed to add customer: ' + error.message);
    }
}

async function viewCustomer(customerId) {
    try {
        const customer = await apiRequest(`customers.php?id=${customerId}`);
        
        // Get customer's purchase history
        const purchaseHistory = await getCustomerPurchaseHistory(customerId);
        
        const purchasesHTML = purchaseHistory.map(purchase => `
            <tr>
                <td>${purchase.invoice_number}</td>
                <td>${formatDate(purchase.sale_date)}</td>
                <td>${purchase.item_count}</td>
                <td>$${parseFloat(purchase.total_amount).toFixed(2)}</td>
                <td><span class="badge badge-${getStatusClass(purchase.payment_status)}">${purchase.payment_status}</span></td>
            </tr>
        `).join('');
        
        showModal(`Customer Details - ${customer.name}`, `
            <div class="customer-details">
                <div class="customer-info">
                    <h5>Contact Information</h5>
                    <p><strong>Name:</strong> ${customer.name}</p>
                    <p><strong>Email:</strong> ${customer.email || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${customer.phone || 'N/A'}</p>
                    <p><strong>Address:</strong> ${customer.address || 'N/A'}</p>
                    <p><strong>City:</strong> ${customer.city || 'N/A'}</p>
                    <p><strong>State:</strong> ${customer.state || 'N/A'}</p>
                    <p><strong>Country:</strong> ${customer.country || 'N/A'}</p>
                    <p><strong>Birth Date:</strong> ${customer.birth_date ? formatDate(customer.birth_date) : 'N/A'}</p>
                    <p><strong>Anniversary:</strong> ${customer.anniversary_date ? formatDate(customer.anniversary_date) : 'N/A'}</p>
                    <p><strong>Status:</strong> <span class="badge badge-${customer.is_active ? 'success' : 'secondary'}">${customer.is_active ? 'Active' : 'Inactive'}</span></p>
                </div>
                
                ${customer.preferences ? `
                    <div class="customer-preferences">
                        <h5>Preferences</h5>
                        <p>${customer.preferences}</p>
                    </div>
                ` : ''}
                
                ${customer.notes ? `
                    <div class="customer-notes">
                        <h5>Notes</h5>
                        <p>${customer.notes}</p>
                    </div>
                ` : ''}
                
                <div class="customer-purchase-history">
                    <h5>Purchase History</h5>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${purchasesHTML}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="customer-summary">
                    <h5>Summary</h5>
                    <p><strong>Total Purchases:</strong> ${purchaseHistory.length}</p>
                    <p><strong>Total Spent:</strong> $${purchaseHistory.reduce((sum, p) => sum + parseFloat(p.total_amount), 0).toFixed(2)}</p>
                    <p><strong>Average Purchase:</strong> $${purchaseHistory.length > 0 ? (purchaseHistory.reduce((sum, p) => sum + parseFloat(p.total_amount), 0) / purchaseHistory.length).toFixed(2) : '0.00'}</p>
                    <p><strong>Last Purchase:</strong> ${purchaseHistory.length > 0 ? formatDate(purchaseHistory[0].sale_date) : 'Never'}</p>
                </div>
            </div>
        `);
        
    } catch (error) {
        showError('Failed to load customer details: ' + error.message);
    }
}

async function getCustomerPurchaseHistory(customerId) {
    try {
        const data = await apiRequest(`sales.php?customer_id=${customerId}`);
        return data.sales || [];
    } catch (error) {
        console.error('Failed to load customer purchase history:', error);
        return [];
    }
}

function editCustomer(customerId) {
    console.log('Edit customer:', customerId);
    // Load customer data and show edit modal
    showEditCustomerModal(customerId);
}

async function showEditCustomerModal(customerId) {
    try {
        const customer = await apiRequest(`customers.php?id=${customerId}`);
        
        showModal('Edit Customer', `
            <form id="editCustomerForm">
                <div class="form-group">
                    <label class="form-label">Customer Name *</label>
                    <input type="text" class="form-control" id="editCustomerName" value="${customer.name}" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editCustomerEmail" value="${customer.email || ''}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="editCustomerPhone" value="${customer.phone || ''}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control form-textarea" id="editCustomerAddress">${customer.address || ''}</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Preferences</label>
                    <textarea class="form-control form-textarea" id="editCustomerPreferences">${customer.preferences || ''}</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control form-textarea" id="editCustomerNotes">${customer.notes || ''}</textarea>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="editCustomerActive" ${customer.is_active ? 'checked' : ''}>
                        <label class="form-check-label" for="editCustomerActive">Active</label>
                    </div>
                </div>
            </form>
        `, `
            <button class="btn btn-primary" onclick="updateCustomer(${customerId})">Update Customer</button>
            <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
        `);
        
    } catch (error) {
        showError('Failed to load customer data: ' + error.message);
    }
}

async function updateCustomer(customerId) {
    const customerData = {
        name: document.getElementById('editCustomerName').value,
        email: document.getElementById('editCustomerEmail').value,
        phone: document.getElementById('editCustomerPhone').value,
        address: document.getElementById('editCustomerAddress').value,
        preferences: document.getElementById('editCustomerPreferences').value,
        notes: document.getElementById('editCustomerNotes').value,
        is_active: document.getElementById('editCustomerActive').checked
    };
    
    try {
        await apiRequest(`customers.php?id=${customerId}`, {
            method: 'PUT',
            body: customerData
        });
        
        closeModal('dynamicModal');
        showSuccess('Customer updated successfully!');
        loadCustomers();
        
    } catch (error) {
        showError('Failed to update customer: ' + error.message);
    }
}

function deleteCustomer(customerId) {
    if (confirm('Are you sure you want to delete this customer?')) {
        apiRequest(`customers.php?id=${customerId}`, { method: 'DELETE' })
            .then(() => {
                showSuccess('Customer deleted successfully!');
                loadCustomers();
            })
            .catch(error => {
                showError('Failed to delete customer: ' + error.message);
            });
    }
}

function searchCustomers() {
    const searchTerm = document.getElementById('customerSearch').value;
    
    if (searchTerm) {
        apiRequest(`customers.php?search=${searchTerm}`)
            .then(data => {
                renderCustomers(data);
            })
            .catch(error => {
                showError('Failed to search customers: ' + error.message);
            });
    } else {
        loadCustomers();
    }
}
