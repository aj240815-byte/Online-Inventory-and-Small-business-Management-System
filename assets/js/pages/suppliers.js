/**
 * Suppliers Page Functions
 * JIMS - Jewellery Inventory Management System
 */

// Suppliers Functions
async function loadSuppliers() {
    try {
        showLoading();
        const data = await apiRequest('suppliers.php');
        renderSuppliers(data);
    } catch (error) {
        console.error('Failed to load suppliers:', error);
        renderSuppliersError();
    } finally {
        hideLoading();
    }
}

function renderSuppliers(data) {
    const html = `
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Suppliers</h3>
                <div>
                    <button class="btn btn-primary" onclick="showAddSupplierModal()">Add Supplier</button>
                </div>
            </div>
            <div class="card-body">
                <div class="search-bar">
                    <input type="text" class="form-control search-input" placeholder="Search suppliers..." id="supplierSearch">
                    <button class="btn btn-outline" onclick="searchSuppliers()">Search</button>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Products</th>
                                <th>Total Purchase</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.suppliers.map(supplier => `
                                <tr>
                                    <td>${supplier.name}</td>
                                    <td>${supplier.contact_person || 'N/A'}</td>
                                    <td>${supplier.email || 'N/A'}</td>
                                    <td>${supplier.phone || 'N/A'}</td>
                                    <td>${supplier.product_count}</td>
                                    <td>$${parseFloat(supplier.total_purchase_value).toFixed(2)}</td>
                                    <td>
                                        <span class="badge badge-${supplier.is_active ? 'success' : 'secondary'}">
                                            ${supplier.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="btn btn-sm btn-primary" onclick="viewSupplier(${supplier.id})">View</button>
                                            <button class="btn btn-sm btn-warning" onclick="editSupplier(${supplier.id})">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteSupplier(${supplier.id})">Delete</button>
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

function renderSuppliersError() {
    contentArea.innerHTML = `
        <div class="card">
            <div class="card-body text-center">
                <h3>Suppliers</h3>
                <p>Unable to load suppliers data. Please check your database connection.</p>
                <button class="btn btn-primary" onclick="loadSuppliers()">Retry</button>
            </div>
        </div>
    `;
}

function showAddSupplierModal() {
    showModal('Add Supplier', `
        <form id="supplierForm">
            <div class="form-group">
                <label class="form-label">Supplier Name *</label>
                <input type="text" class="form-control" id="supplierName" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Contact Person</label>
                <input type="text" class="form-control" id="supplierContactPerson">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="supplierEmail">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="supplierPhone">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea class="form-control form-textarea" id="supplierAddress"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" id="supplierCity">
                </div>
                <div class="form-group">
                    <label class="form-label">State/Province</label>
                    <input type="text" class="form-control" id="supplierState">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Postal Code</label>
                    <input type="text" class="form-control" id="supplierPostalCode">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" class="form-control" id="supplierCountry">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tax ID</label>
                    <input type="text" class="form-control" id="supplierTaxId">
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Terms</label>
                    <input type="text" class="form-control" id="supplierPaymentTerms" placeholder="e.g., Net 30">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea class="form-control form-textarea" id="supplierNotes" placeholder="Additional notes..."></textarea>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="supplierActive" checked>
                    <label class="form-check-label" for="supplierActive">Active</label>
                </div>
            </div>
        </form>
    `, `
        <button class="btn btn-primary" onclick="saveSupplier()">Save Supplier</button>
        <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
    `);
}

async function saveSupplier() {
    const supplierData = {
        name: document.getElementById('supplierName').value,
        contact_person: document.getElementById('supplierContactPerson').value,
        email: document.getElementById('supplierEmail').value,
        phone: document.getElementById('supplierPhone').value,
        address: document.getElementById('supplierAddress').value,
        city: document.getElementById('supplierCity').value,
        state: document.getElementById('supplierState').value,
        postal_code: document.getElementById('supplierPostalCode').value,
        country: document.getElementById('supplierCountry').value,
        tax_id: document.getElementById('supplierTaxId').value,
        payment_terms: document.getElementById('supplierPaymentTerms').value,
        notes: document.getElementById('supplierNotes').value,
        is_active: document.getElementById('supplierActive').checked
    };
    
    // Validate required fields
    if (!supplierData.name) {
        showError('Supplier name is required');
        return;
    }
    
    try {
        await apiRequest('suppliers.php', {
            method: 'POST',
            body: supplierData
        });
        
        closeModal('dynamicModal');
        showSuccess('Supplier added successfully!');
        loadSuppliers();
        
    } catch (error) {
        showError('Failed to add supplier: ' + error.message);
    }
}

async function viewSupplier(supplierId) {
    try {
        const supplier = await apiRequest(`suppliers.php?id=${supplierId}`);
        
        const productsHTML = supplier.products.map(product => `
            <tr>
                <td>${product.name}</td>
                <td>${product.sku}</td>
                <td>${product.stock_quantity}</td>
                <td>$${parseFloat(product.cost_price).toFixed(2)}</td>
                <td>$${parseFloat(product.selling_price).toFixed(2)}</td>
            </tr>
        `).join('');
        
        const purchaseOrdersHTML = supplier.recent_purchase_orders.map(po => `
            <tr>
                <td>${po.po_number}</td>
                <td>${formatDate(po.order_date)}</td>
                <td>${po.expected_delivery_date ? formatDate(po.expected_delivery_date) : 'N/A'}</td>
                <td><span class="badge badge-${getPOStatusClass(po.status)}">${po.status}</span></td>
                <td>$${parseFloat(po.total_amount).toFixed(2)}</td>
            </tr>
        `).join('');
        
        showModal(`Supplier Details - ${supplier.name}`, `
            <div class="supplier-details">
                <div class="supplier-info">
                    <h5>Contact Information</h5>
                    <p><strong>Name:</strong> ${supplier.name}</p>
                    <p><strong>Contact Person:</strong> ${supplier.contact_person || 'N/A'}</p>
                    <p><strong>Email:</strong> ${supplier.email || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${supplier.phone || 'N/A'}</p>
                    <p><strong>Address:</strong> ${supplier.address || 'N/A'}</p>
                    <p><strong>City:</strong> ${supplier.city || 'N/A'}</p>
                    <p><strong>State:</strong> ${supplier.state || 'N/A'}</p>
                    <p><strong>Country:</strong> ${supplier.country || 'N/A'}</p>
                    <p><strong>Tax ID:</strong> ${supplier.tax_id || 'N/A'}</p>
                    <p><strong>Payment Terms:</strong> ${supplier.payment_terms || 'N/A'}</p>
                    <p><strong>Status:</strong> <span class="badge badge-${supplier.is_active ? 'success' : 'secondary'}">${supplier.is_active ? 'Active' : 'Inactive'}</span></p>
                </div>
                
                <div class="supplier-products">
                    <h5>Products (${supplier.products.length})</h5>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Cost Price</th>
                                    <th>Selling Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${productsHTML}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="supplier-purchase-orders">
                    <h5>Recent Purchase Orders</h5>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Order Date</th>
                                    <th>Expected Delivery</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${purchaseOrdersHTML}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                ${supplier.notes ? `
                    <div class="supplier-notes">
                        <h5>Notes</h5>
                        <p>${supplier.notes}</p>
                    </div>
                ` : ''}
            </div>
        `);
        
    } catch (error) {
        showError('Failed to load supplier details: ' + error.message);
    }
}

function getPOStatusClass(status) {
    switch(status.toLowerCase()) {
        case 'delivered': return 'success';
        case 'shipped': return 'info';
        case 'confirmed': return 'primary';
        case 'pending': return 'warning';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function editSupplier(supplierId) {
    console.log('Edit supplier:', supplierId);
    // Load supplier data and show edit modal
    showEditSupplierModal(supplierId);
}

async function showEditSupplierModal(supplierId) {
    try {
        const supplier = await apiRequest(`suppliers.php?id=${supplierId}`);
        
        showModal('Edit Supplier', `
            <form id="editSupplierForm">
                <div class="form-group">
                    <label class="form-label">Supplier Name *</label>
                    <input type="text" class="form-control" id="editSupplierName" value="${supplier.name}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input type="text" class="form-control" id="editSupplierContactPerson" value="${supplier.contact_person || ''}">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editSupplierEmail" value="${supplier.email || ''}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="editSupplierPhone" value="${supplier.phone || ''}">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control form-textarea" id="editSupplierAddress">${supplier.address || ''}</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control form-textarea" id="editSupplierNotes">${supplier.notes || ''}</textarea>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="editSupplierActive" ${supplier.is_active ? 'checked' : ''}>
                        <label class="form-check-label" for="editSupplierActive">Active</label>
                    </div>
                </div>
            </form>
        `, `
            <button class="btn btn-primary" onclick="updateSupplier(${supplierId})">Update Supplier</button>
            <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
        `);
        
    } catch (error) {
        showError('Failed to load supplier data: ' + error.message);
    }
}

async function updateSupplier(supplierId) {
    const supplierData = {
        name: document.getElementById('editSupplierName').value,
        contact_person: document.getElementById('editSupplierContactPerson').value,
        email: document.getElementById('editSupplierEmail').value,
        phone: document.getElementById('editSupplierPhone').value,
        address: document.getElementById('editSupplierAddress').value,
        notes: document.getElementById('editSupplierNotes').value,
        is_active: document.getElementById('editSupplierActive').checked
    };
    
    try {
        await apiRequest(`suppliers.php?id=${supplierId}`, {
            method: 'PUT',
            body: supplierData
        });
        
        closeModal('dynamicModal');
        showSuccess('Supplier updated successfully!');
        loadSuppliers();
        
    } catch (error) {
        showError('Failed to update supplier: ' + error.message);
    }
}

function deleteSupplier(supplierId) {
    if (confirm('Are you sure you want to delete this supplier?')) {
        apiRequest(`suppliers.php?id=${supplierId}`, { method: 'DELETE' })
            .then(() => {
                showSuccess('Supplier deleted successfully!');
                loadSuppliers();
            })
            .catch(error => {
                showError('Failed to delete supplier: ' + error.message);
            });
    }
}

function searchSuppliers() {
    const searchTerm = document.getElementById('supplierSearch').value;
    
    if (searchTerm) {
        apiRequest(`suppliers.php?search=${searchTerm}`)
            .then(data => {
                renderSuppliers(data);
            })
            .catch(error => {
                showError('Failed to search suppliers: ' + error.message);
            });
    } else {
        loadSuppliers();
    }
}
