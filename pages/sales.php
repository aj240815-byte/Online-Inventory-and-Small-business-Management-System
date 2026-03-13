<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - JIMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="JIMS Sales - Manage Transactions">
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
        
        .btn-success {
            background: #28a745;
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
            max-width: 700px;
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
        
        .sale-item {
            display: flex;
            gap: 15px;
            align-items: end;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .sale-item .form-group {
            margin-bottom: 0;
            flex: 1;
        }
        
        .btn-remove-item {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            height: fit-content;
        }
        
        .total-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-top: 20px;
            text-align: right;
        }
        
        .total-section h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.5rem;
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
                            <a href="sales.php" class="sidebar-link active" data-page="sales">
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
                    <h1>Sales Management</h1>
                    <p>Process and track your sales transactions</p>
                </div>

                <!-- Sales Content -->
                <div class="sales-section">
                    <!-- Section Header with Button -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h2>Recent Sales</h2>
                            <p>View and manage all your sales transactions.</p>
                        </div>
                        <button class="btn btn-primary" onclick="openSaleModal()">
                            <i class="fas fa-plus"></i> New Sale
                        </button>
                    </div>

                    <!-- Sales Table -->
                    <div class="table-container">
                        <table class="data-table" id="salesTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                                        <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                        <p>No sales found. Process your first sale to get started.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit Sale Modal -->
    <div class="modal" id="saleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">New Sale</h2>
                <button class="modal-close" onclick="closeSaleModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="saleForm">
                    <input type="hidden" id="saleId" value="">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="invoiceNumber">Invoice Number</label>
                            <input type="text" id="invoiceNumber" name="invoiceNumber" placeholder="Auto-generated">
                        </div>
                        
                        <div class="form-group">
                            <label for="saleDate">Date *</label>
                            <input type="date" id="saleDate" name="saleDate" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer">Customer *</label>
                            <select id="customer" name="customer" required>
                                <option value="">Select Customer</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select id="paymentMethod" name="paymentMethod">
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <h3 style="margin-top: 30px; margin-bottom: 15px; color: #2c3e50;">Items</h3>
                    <div id="saleItems">
                        <div class="sale-item">
                            <div class="form-group">
                                <label>Product *</label>
                                <select name="product[]" class="product-select" required>
                                    <option value="">Select Product</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantity *</label>
                                <input type="number" name="quantity[]" class="quantity-input" min="1" value="1" required>
                            </div>
                            <div class="form-group">
                                <label>Unit Price (UGX)</label>
                                <input type="number" name="price[]" class="price-input" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <button type="button" class="btn-remove-item" onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary" onclick="addItem()" style="margin-bottom: 20px;">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                    
                    <div class="total-section">
                        <h3>Total: UGX <span id="totalAmount">0.00</span></h3>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" placeholder="Additional notes about this sale..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeSaleModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteSale()" style="display: none;">Delete</button>
                <button type="button" class="btn btn-primary" onclick="saveSale()">Save Sale</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #2c3e50; color: white; padding: 20px 0; margin-top: auto;">
        <div class="container">
            <div style="text-align: center;">
                <p style="margin: 0; font-size: 0.9rem;"> 2026 JIMS - Jewellery Inventory Management System</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        const API_BASE = '../api/index.php?endpoint=sales';
        const CUSTOMERS_API = '../api/index.php?endpoint=customers';
        const PRODUCTS_API = '../api/index.php?endpoint=products';
        let sales = [];
        let customers = [];
        let products = [];
        let currentEditId = null;
        let itemCounter = 1;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            Promise.all([
                loadCustomers(),
                loadProductsForSelect()
            ]).then(() => {
                loadSales();
                setupEventListeners();
            });
        });

        async function loadCustomers() {
            try {
                const res = await fetch(CUSTOMERS_API);
                const data = await res.json();
                if (data.success) {
                    customers = data.data;
                    const customerSelect = document.getElementById('customer');
                    customerSelect.innerHTML = '<option value=\"\">Select Customer</option>' +
                        customers.map(c => `<option value=\"${c.id}\">${c.name}</option>`).join('') +
                        '<option value=\"walk-in\">Walk-in Customer</option>';
                }
            } catch (e) {
                console.error('Error loading customers:', e);
            }
        }

        async function loadProductsForSelect() {
            try {
                const res = await fetch(PRODUCTS_API);
                const data = await res.json();
                if (data.success) {
                    products = data.data;
                    refreshProductSelects();
                }
            } catch (e) {
                console.error('Error loading products for sales form:', e);
            }
        }

        function productOptionsHtml(selectedId = '') {
            return '<option value=\"\">Select Product</option>' +
                products.map(p => `
                    <option value=\"${p.id}\" data-price=\"${p.selling_price}\" ${String(p.id) === String(selectedId) ? 'selected' : ''}>
                        ${p.name}
                    </option>
                `).join('');
        }

        function refreshProductSelects() {
            document.querySelectorAll('.product-select').forEach(select => {
                const current = select.value;
                select.innerHTML = productOptionsHtml(current);
            });
        }

        // Load sales from database
        async function loadSales() {
            try {
                const response = await fetch(API_BASE);
                const data = await response.json();
                if (data.success) {
                    sales = data.data.map(s => ({
                        id: s.id,
                        invoiceNumber: s.invoice_number,
                        customer: s.customer_name || 'Walk-in',
                        date: s.sale_date,
                        amount: parseFloat(s.total_amount),
                        status: s.payment_status === 'paid' ? 'Completed' : s.payment_status === 'pending' ? 'Pending' : 'Cancelled',
                        paymentMethod: s.payment_method,
                        notes: s.notes
                    }));
                    renderSalesTable();
                }
            } catch (error) {
                console.error('Error loading sales:', error);
                showNotification('Error loading sales', 'error');
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            document.getElementById('saleItems').addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity-input')) {
                    calculateTotal();
                }
            });
        }

        // Render sales table
        function renderSalesTable() {
            const tbody = document.querySelector('#salesTable tbody');
            
            if (sales.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                            <p>No sales found. Process your first sale to get started.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = sales.map(sale => `
                <tr>
                    <td>${sale.invoiceNumber}</td>
                    <td>${sale.customer}</td>
                    <td>${sale.date}</td>
                    <td>UGX ${sale.amount.toFixed(2)}</td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; background: ${getStatusColor(sale.status)}; color: white;">
                            ${sale.status}
                        </span>
                    </td>
                    <td>
                        <button class="action-btn btn-edit" onclick="editSale(${sale.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn btn-delete" onclick="confirmDelete(${sale.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Get status color
        function getStatusColor(status) {
            const colors = {
                'Completed': '#28a745',
                'Pending': '#ffc107',
                'Cancelled': '#dc3545'
            };
            return colors[status] || '#6c757d';
        }

        // Open sale modal for adding
        function openSaleModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'New Sale';
            document.getElementById('saleForm').reset();
            document.getElementById('deleteBtn').style.display = 'none';
            document.getElementById('saleDate').value = new Date().toISOString().split('T')[0];
            
            // Reset items to one empty item
            document.getElementById('saleItems').innerHTML = `
                <div class="sale-item">
                    <div class="form-group">
                        <label>Product *</label>
                        <select name="product[]" class="product-select" required>
                            ${productOptionsHtml()}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity[]" class="quantity-input" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Unit Price (UGX)</label>
                        <input type="number" name="price[]" class="price-input" min="0" step="0.01" placeholder="0.00">
                    </div>
                    <button type="button" class="btn-remove-item" onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            calculateTotal();
            document.getElementById('saleModal').style.display = 'block';
        }

        // Open sale modal for editing
        function editSale(id) {
            currentEditId = id;
            const sale = sales.find(s => s.id === id);
            
            if (sale) {
                document.getElementById('modalTitle').textContent = 'Edit Sale';
                document.getElementById('saleId').value = sale.id;
                document.getElementById('invoiceNumber').value = sale.invoiceNumber;
                document.getElementById('saleDate').value = sale.date;
                document.getElementById('customer').value = sale.customer;
                document.getElementById('paymentMethod').value = sale.paymentMethod;
                document.getElementById('status').value = sale.status;
                document.getElementById('notes').value = sale.notes || '';
                
                // Render items (client-side detail loading not implemented; start with single blank row)
                if (sale.items && sale.items.length > 0) {
                    document.getElementById('saleItems').innerHTML = sale.items.map(item => `
                        <div class="sale-item">
                            <div class="form-group">
                                <label>Product *</label>
                                <select name="product[]" class="product-select" required>
                                    ${productOptionsHtml(item.productId)}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantity *</label>
                                <input type="number" name="quantity[]" class="quantity-input" min="1" value="${item.quantity}" required>
                            </div>
                            <div class="form-group">
                                <label>Unit Price (UGX)</label>
                                <input type="number" name="price[]" class="price-input" min="0" step="0.01" value="${item.price}">
                            </div>
                            <button type="button" class="btn-remove-item" onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('');
                } else {
                    document.getElementById('saleItems').innerHTML = `
                        <div class="sale-item">
                            <div class="form-group">
                                <label>Product *</label>
                                <select name="product[]" class="product-select" required>
                                    ${productOptionsHtml()}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantity *</label>
                                <input type="number" name="quantity[]" class="quantity-input" min="1" value="1" required>
                            </div>
                            <div class="form-group">
                                <label>Unit Price (UGX)</label>
                                <input type="number" name="price[]" class="price-input" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <button type="button" class="btn-remove-item" onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
                
                calculateTotal();
                document.getElementById('deleteBtn').style.display = 'inline-block';
                document.getElementById('saleModal').style.display = 'block';
            }
        }

        // Close sale modal
        function closeSaleModal() {
            document.getElementById('saleModal').style.display = 'none';
            document.getElementById('saleForm').reset();
            currentEditId = null;
        }

        // Add item row
        function addItem() {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'sale-item';
            itemDiv.innerHTML = `
                <div class="form-group">
                    <label>Product *</label>
                    <select name="product[]" class="product-select" required>
                        ${productOptionsHtml()}
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity *</label>
                    <input type="number" name="quantity[]" class="quantity-input" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label>Unit Price (UGX)</label>
                    <input type="number" name="price[]" class="price-input" min="0" step="0.01" placeholder="0.00">
                </div>
                <button type="button" class="btn-remove-item" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            document.getElementById('saleItems').appendChild(itemDiv);
            calculateTotal();
        }

        // Remove item row
        function removeItem(button) {
            const items = document.querySelectorAll('.sale-item');
            if (items.length > 1) {
                button.parentElement.remove();
                calculateTotal();
            } else {
                alert('At least one item is required.');
            }
        }

        // Calculate total
        function calculateTotal() {
            let total = 0;
            const items = document.querySelectorAll('.sale-item');
            
            items.forEach(item => {
                const productSelect = item.querySelector('.product-select');
                const quantityInput = item.querySelector('.quantity-input');
                const priceInput = item.querySelector('.price-input');
                
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const price = priceInput.value ? parseFloat(priceInput.value) : (selectedOption.dataset.price ? parseFloat(selectedOption.dataset.price) : 0);
                const quantity = parseInt(quantityInput.value) || 1;
                
                total += price * quantity;
            });
            
            document.getElementById('totalAmount').textContent = total.toFixed(2);
        }

        // Save sale to database
        async function saveSale() {
            const form = document.getElementById('saleForm');
            const formData = new FormData(form);
            
            const total = parseFloat(document.getElementById('totalAmount').textContent);
            const customer = formData.get('customer');
            
            if (!customer) {
                alert('Please select a customer.');
                return;
            }
            
            if (total <= 0) {
                alert('Please add at least one item with a valid price.');
                return;
            }

            const saleData = {
                invoiceNumber: formData.get('invoiceNumber') || generateInvoiceNumber(),
                customerId: customer === 'walk-in' ? null : customer,
                date: formData.get('saleDate'),
                total: total,
                status: formData.get('status'),
                paymentMethod: formData.get('paymentMethod'),
                notes: formData.get('notes'),
                items: []
            };

            // Get items
            const itemElements = document.querySelectorAll('.sale-item');
            itemElements.forEach(item => {
                const productSelect = item.querySelector('.product-select');
                const quantityInput = item.querySelector('.quantity-input');
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                
                if (selectedOption.value) {
                    saleData.items.push({
                        productId: selectedOption.value,
                        quantity: parseInt(quantityInput.value) || 1,
                        price: parseFloat(selectedOption.dataset.price) || 0
                    });
                }
            });

            try {
                const response = await fetch(API_BASE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(saleData)
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadSales();
                    closeSaleModal();
                    showNotification('Sale processed successfully!');
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving sale:', error);
                alert('Error processing sale');
            }
        }

        // Delete sale
        async function deleteSale() {
            if (currentEditId && confirm('Are you sure?')) {
                await deleteSaleById(currentEditId);
                closeSaleModal();
            }
        }

        // Delete sale by ID
        async function deleteSaleById(id) {
            try {
                const response = await fetch(API_BASE + '&id=' + id, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadSales();
                    showNotification('Sale deleted!');
                }
            } catch (error) {
                console.error('Error deleting sale:', error);
                alert('Error deleting sale');
            }
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this sale?')) {
                deleteSaleById(id);
            }
        }

        // Generate invoice number
        function generateInvoiceNumber() {
            const prefix = 'INV';
            const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
            const date = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            return `${prefix}-${date}-${random}`;
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
            const modal = document.getElementById('saleModal');
            if (event.target === modal) {
                closeSaleModal();
            }
        }
    </script>
</body>
</html>
