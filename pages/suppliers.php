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
    <meta name="description" content="JIMS Suppliers - Manage Vendor Information">
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
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
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
            background: #f8f9fa;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }
        
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .modal-body {
            padding: 30px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2c3e50;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            margin: 0 2px;
        }
        
        .btn-edit {
            background: #28a745;
            color: white;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
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
                    <h1>Suppliers Management</h1>
                    <p>Manage your vendor and supplier information</p>
                </div>

                <!-- Suppliers Content -->
                <div class="suppliers-section">
                    <!-- Section Header with Button -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h2>Supplier Directory</h2>
                            <p>Add, edit, and manage your supplier database.</p>
                        </div>
                        <button class="btn btn-primary" onclick="openSupplierModal()">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </button>
                    </div>

                    <!-- Suppliers Table -->
                    <div class="table-container">
                        <table class="data-table" id="suppliersTable">
                            <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Products</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">
                                            <i class="fas fa-truck" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                            <p>No suppliers found. Add your first supplier to get started.</p>
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

    <!-- Add/Edit Supplier Modal -->
    <div class="modal" id="supplierModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Supplier</h2>
                <button class="modal-close" onclick="closeSupplierModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierForm">
                    <input type="hidden" id="supplierId" value="">
                    
                    <div class="form-group">
                        <label for="companyName">Company Name *</label>
                        <input type="text" id="companyName" name="companyName" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactPerson">Contact Person *</label>
                            <input type="text" id="contactPerson" name="contactPerson" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" placeholder="Enter supplier address..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="products">Products Supplied</label>
                        <input type="text" id="products" name="products" placeholder="e.g., Gold, Silver, Diamonds">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="paymentTerms">Payment Terms</label>
                            <select id="paymentTerms" name="paymentTerms">
                                <option value="Net 30">Net 30</option>
                                <option value="Net 60">Net 60</option>
                                <option value="Net 90">Net 90</option>
                                <option value="COD">COD</option>
                                <option value="Advance">Advance</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="leadTime">Lead Time (days)</label>
                            <input type="number" id="leadTime" name="leadTime" min="1" placeholder="7">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeSupplierModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteSupplier()" style="display: none;">Delete</button>
                <button type="button" class="btn btn-primary" onclick="saveSupplier()">Save Supplier</button>
            </div>
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

    <!-- JavaScript -->
    <script>
        const API_BASE = '../api/index.php?endpoint=suppliers';
        let suppliers = [];
        let currentEditId = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadSuppliers();
        });

        // Load suppliers from database
        async function loadSuppliers() {
            try {
                const response = await fetch(API_BASE);
                const data = await response.json();
                if (data.success) {
                    suppliers = data.data.map(s => ({
                        id: s.id,
                        companyName: s.name,
                        contactPerson: s.contact_person,
                        email: s.email,
                        phone: s.phone,
                        status: s.is_active ? 'Active' : 'Inactive',
                        address: s.address,
                        products: s.notes || '',
                        paymentTerms: s.payment_terms || 'Net 30',
                        leadTime: 7
                    }));
                    renderSuppliersTable();
                }
            } catch (error) {
                console.error('Error loading suppliers:', error);
                showNotification('Error loading suppliers', 'error');
            }
        }

        // Render suppliers table
        function renderSuppliersTable() {
            const tbody = document.querySelector('#suppliersTable tbody');
            
            if (suppliers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-truck" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                            <p>No suppliers found. Add your first supplier to get started.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = suppliers.map(supplier => `
                <tr>
                    <td>${supplier.companyName}</td>
                    <td>${supplier.contactPerson}</td>
                    <td>${supplier.email}</td>
                    <td>${supplier.phone}</td>
                    <td>${supplier.products}</td>
                    <td>
                        <button class="action-btn btn-edit" onclick="editSupplier(${supplier.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn btn-delete" onclick="confirmDelete(${supplier.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Open supplier modal for adding
        function openSupplierModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'Add New Supplier';
            document.getElementById('supplierForm').reset();
            document.getElementById('deleteBtn').style.display = 'none';
            document.getElementById('supplierModal').style.display = 'block';
        }

        // Open supplier modal for editing
        function editSupplier(id) {
            currentEditId = id;
            const supplier = suppliers.find(s => s.id === id);
            
            if (supplier) {
                document.getElementById('modalTitle').textContent = 'Edit Supplier';
                document.getElementById('supplierId').value = supplier.id;
                document.getElementById('companyName').value = supplier.companyName;
                document.getElementById('contactPerson').value = supplier.contactPerson;
                document.getElementById('email').value = supplier.email;
                document.getElementById('phone').value = supplier.phone;
                document.getElementById('status').value = supplier.status || 'Active';
                document.getElementById('address').value = supplier.address || '';
                document.getElementById('products').value = supplier.products || '';
                document.getElementById('paymentTerms').value = supplier.paymentTerms || 'Net 30';
                document.getElementById('leadTime').value = supplier.leadTime || '';
                document.getElementById('deleteBtn').style.display = 'inline-block';
                document.getElementById('supplierModal').style.display = 'block';
            }
        }

        // Close supplier modal
        function closeSupplierModal() {
            document.getElementById('supplierModal').style.display = 'none';
            document.getElementById('supplierForm').reset();
            currentEditId = null;
        }

        // Save supplier to database
        async function saveSupplier() {
            const form = document.getElementById('supplierForm');
            const formData = new FormData(form);
            
            const supplierData = {
                companyName: formData.get('companyName'),
                contactPerson: formData.get('contactPerson'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                status: formData.get('status'),
                address: formData.get('address'),
                products: formData.get('products'),
                paymentTerms: formData.get('paymentTerms'),
                leadTime: parseInt(formData.get('leadTime')) || 7
            };

            if (!supplierData.companyName || !supplierData.contactPerson || !supplierData.email || !supplierData.phone) {
                alert('Please fill in all required fields.');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(supplierData.email)) {
                alert('Please enter a valid email address.');
                return;
            }

            try {
                const method = currentEditId ? 'PUT' : 'POST';
                if (currentEditId) supplierData.id = currentEditId;
                
                const response = await fetch(API_BASE, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(supplierData)
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadSuppliers();
                    closeSupplierModal();
                    showNotification(currentEditId ? 'Supplier updated!' : 'Supplier added!');
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving supplier:', error);
                alert('Error saving supplier');
            }
        }

        // Confirm delete
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this supplier?')) {
                deleteSupplierById(id);
            }
        }

        // Delete supplier
        async function deleteSupplier() {
            if (currentEditId && confirm('Are you sure?')) {
                await deleteSupplierById(currentEditId);
                closeSupplierModal();
            }
        }

        // Delete supplier by ID
        async function deleteSupplierById(id) {
            try {
                const response = await fetch(API_BASE + '&id=' + id, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadSuppliers();
                    showNotification('Supplier deleted!');
                }
            } catch (error) {
                console.error('Error deleting supplier:', error);
                alert('Error deleting supplier');
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#dc3545'};
                color: white;
                padding: 15px 20px;
                border-radius: 6px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 2000;
                font-size: 0.9rem;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => document.body.removeChild(notification), 3000);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('supplierModal');
            if (event.target === modal) closeSupplierModal();
        }
    </script>
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-truck" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                            <p>No suppliers found. Add your first supplier to get started.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = suppliers.map(supplier => `
                <tr>
                    <td>${supplier.companyName}</td>
                    <td>${supplier.contactPerson}</td>
                    <td>${supplier.email}</td>
                    <td>${supplier.phone}</td>
                    <td>${supplier.products}</td>
                    <td>
                        <button class="action-btn btn-edit" onclick="editSupplier(${supplier.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn btn-delete" onclick="confirmDelete(${supplier.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Open supplier modal for adding
        function openSupplierModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'Add New Supplier';
            document.getElementById('supplierForm').reset();
            document.getElementById('deleteBtn').style.display = 'none';
            document.getElementById('supplierModal').style.display = 'block';
        }

        // Open supplier modal for editing
        function editSupplier(id) {
            currentEditId = id;
            const supplier = suppliers.find(s => s.id === id);
            
            if (supplier) {
                document.getElementById('modalTitle').textContent = 'Edit Supplier';
                document.getElementById('supplierId').value = supplier.id;
                document.getElementById('companyName').value = supplier.companyName;
                document.getElementById('contactPerson').value = supplier.contactPerson;
                document.getElementById('email').value = supplier.email;
                document.getElementById('phone').value = supplier.phone;
                document.getElementById('status').value = supplier.status || 'Active';
                document.getElementById('address').value = supplier.address || '';
                document.getElementById('products').value = supplier.products || '';
                document.getElementById('paymentTerms').value = supplier.paymentTerms || 'Net 30';
                document.getElementById('leadTime').value = supplier.leadTime || '';
                document.getElementById('deleteBtn').style.display = 'inline-block';
                document.getElementById('supplierModal').style.display = 'block';
            }
        }

        // Close supplier modal
        function closeSupplierModal() {
            document.getElementById('supplierModal').style.display = 'none';
            document.getElementById('supplierForm').reset();
            currentEditId = null;
        }

        // Save supplier (add or update)
        function saveSupplier() {
            const form = document.getElementById('supplierForm');
            const formData = new FormData(form);
            
            const supplierData = {
                companyName: formData.get('companyName'),
                contactPerson: formData.get('contactPerson'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                status: formData.get('status'),
                address: formData.get('address'),
                products: formData.get('products'),
                paymentTerms: formData.get('paymentTerms'),
                leadTime: parseInt(formData.get('leadTime')) || 7
            };

            // Validation
            if (!supplierData.companyName || !supplierData.contactPerson || !supplierData.email || !supplierData.phone) {
                alert('Please fill in all required fields.');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(supplierData.email)) {
                alert('Please enter a valid email address.');
                return;
            }

            if (currentEditId) {
                // Update existing supplier
                const index = suppliers.findIndex(s => s.id === currentEditId);
                if (index !== -1) {
                    suppliers[index] = { ...suppliers[index], ...supplierData };
                }
            } else {
                // Add new supplier
                supplierData.id = suppliers.length > 0 ? Math.max(...suppliers.map(s => s.id)) + 1 : 1;
                suppliers.push(supplierData);
            }

            renderSuppliersTable();
            closeSupplierModal();
            showNotification(currentEditId ? 'Supplier updated successfully!' : 'Supplier added successfully!');
        }

        // Confirm delete
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this supplier?')) {
                deleteSupplierById(id);
            }
        }

        // Delete supplier
        function deleteSupplier() {
            if (currentEditId && confirm('Are you sure you want to delete this supplier?')) {
                deleteSupplierById(currentEditId);
                closeSupplierModal();
            }
        }

        // Delete supplier by ID
        function deleteSupplierById(id) {
            suppliers = suppliers.filter(s => s.id !== id);
            renderSuppliersTable();
            showNotification('Supplier deleted successfully!');
        }

        // Show notification
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 15px 20px;
                border-radius: 6px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 2000;
                font-size: 0.9rem;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 3000);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('supplierModal');
            if (event.target === modal) closeSupplierModal();
        }
    </script>
</body>
</html>
