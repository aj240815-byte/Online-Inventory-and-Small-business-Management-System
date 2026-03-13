<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - JIMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="JIMS Customers - Manage Client Information">
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
                            <a href="customers.php" class="sidebar-link active" data-page="customers">
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
                <!-- Page Header -->
                <div class="page-header">
                    <h1>Customers Management</h1>
                    <p>Manage your customer information and contacts</p>
                </div>

                <!-- Customers Content -->
                <div class="customers-section">
                    <!-- Section Header with Button -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h2>Customer Directory</h2>
                            <p>Add, edit, and manage your customer database.</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCustomerModal()">
                            <i class="fas fa-plus"></i> Add New Customer
                        </button>
                    </div>

                    <!-- Customers Table -->
                    <div class="table-container">
                        <table class="data-table" id="customersTable">
                            <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">
                                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                            <p>No customers found. Add your first customer to get started.</p>
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

    <!-- Add/Edit Customer Modal -->
    <div class="modal" id="customerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Customer</h2>
                <button class="modal-close" onclick="closeCustomerModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <input type="hidden" id="customerId" value="">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
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
                                <option value="VIP">VIP</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" placeholder="Enter customer address..."></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" placeholder="New York">
                        </div>
                        
                        <div class="form-group">
                            <label for="zipCode">Zip Code</label>
                            <input type="text" id="zipCode" name="zipCode" placeholder="10001">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" placeholder="Additional notes about customer..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteCustomer()" style="display: none;">Delete</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #2c3e50; color: white; padding: 20px 0; margin-top: auto;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin: 0; font-size: 0.9rem;">© 2024 JIMS - Jewellery Inventory Management System</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        const API_BASE = '../api/index.php?endpoint=customers';
        let customers = [];
        let currentEditId = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadCustomers();
        });

        // Load customers from database
        async function loadCustomers() {
            try {
                const response = await fetch(API_BASE);
                const data = await response.json();
                if (data.success) {
                    customers = data.data.map(c => {
                        const nameParts = c.name.split(' ');
                        return {
                            id: c.id,
                            firstName: nameParts[0] || '',
                            lastName: nameParts.slice(1).join(' ') || '',
                            email: c.email,
                            phone: c.phone,
                            status: c.is_active ? 'Active' : 'Inactive',
                            address: c.address,
                            city: c.city,
                            zipCode: c.postal_code,
                            notes: c.notes,
                            registered: c.created_at.split(' ')[0]
                        };
                    });
                    renderCustomersTable();
                }
            } catch (error) {
                console.error('Error loading customers:', error);
                showNotification('Error loading customers', 'error');
            }
        }

        // Render customers table
        function renderCustomersTable() {
            const tbody = document.querySelector('#customersTable tbody');
            
            if (customers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                            <p>No customers found. Add your first customer to get started.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = customers.map(customer => `
                <tr>
                    <td>${customer.firstName} ${customer.lastName}</td>
                    <td>${customer.email}</td>
                    <td>${customer.phone}</td>
                    <td>${customer.city}</td>
                    <td>${customer.registered}</td>
                    <td>
                        <button class="action-btn btn-edit" onclick="editCustomer(${customer.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn btn-delete" onclick="confirmDelete(${customer.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Open customer modal for adding
        function openCustomerModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'Add New Customer';
            document.getElementById('customerForm').reset();
            document.getElementById('deleteBtn').style.display = 'none';
            document.getElementById('customerModal').style.display = 'block';
        }

        // Open customer modal for editing
        function editCustomer(id) {
            currentEditId = id;
            const customer = customers.find(c => c.id === id);
            
            if (customer) {
                document.getElementById('modalTitle').textContent = 'Edit Customer';
                document.getElementById('customerId').value = customer.id;
                document.getElementById('firstName').value = customer.firstName;
                document.getElementById('lastName').value = customer.lastName;
                document.getElementById('email').value = customer.email;
                document.getElementById('phone').value = customer.phone;
                document.getElementById('status').value = customer.status;
                document.getElementById('address').value = customer.address || '';
                document.getElementById('city').value = customer.city || '';
                document.getElementById('zipCode').value = customer.zipCode || '';
                document.getElementById('notes').value = customer.notes || '';
                document.getElementById('deleteBtn').style.display = 'inline-block';
                document.getElementById('customerModal').style.display = 'block';
            }
        }

        // Close customer modal
        function closeCustomerModal() {
            document.getElementById('customerModal').style.display = 'none';
            document.getElementById('customerForm').reset();
            currentEditId = null;
        }

        // Save customer to database
        async function saveCustomer() {
            const form = document.getElementById('customerForm');
            const formData = new FormData(form);
            
            const customerData = {
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                status: formData.get('status'),
                address: formData.get('address'),
                city: formData.get('city'),
                zipCode: formData.get('zipCode'),
                notes: formData.get('notes')
            };

            if (!customerData.firstName || !customerData.lastName || !customerData.email || !customerData.phone) {
                alert('Please fill in all required fields.');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(customerData.email)) {
                alert('Please enter a valid email address.');
                return;
            }

            try {
                const method = currentEditId ? 'PUT' : 'POST';
                if (currentEditId) customerData.id = currentEditId;
                
                const response = await fetch(API_BASE, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(customerData)
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadCustomers();
                    closeCustomerModal();
                    showNotification(currentEditId ? 'Customer updated!' : 'Customer added!');
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving customer:', error);
                alert('Error saving customer');
            }
        }

        // Confirm delete
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this customer?')) {
                deleteCustomerById(id);
            }
        }

        // Delete customer
        async function deleteCustomer() {
            if (currentEditId && confirm('Are you sure?')) {
                await deleteCustomerById(currentEditId);
                closeCustomerModal();
            }
        }

        // Delete customer by ID
        async function deleteCustomerById(id) {
            try {
                const response = await fetch(API_BASE + '&id=' + id, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadCustomers();
                    showNotification('Customer deleted!');
                }
            } catch (error) {
                console.error('Error deleting customer:', error);
                alert('Error deleting customer');
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
            const modal = document.getElementById('customerModal');
            if (event.target === modal) closeCustomerModal();
        }
    </script>
</body>
</html>
