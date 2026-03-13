<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - JIMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="JIMS Products - Manage Inventory">
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
                            <a href="products.php" class="sidebar-link active" data-page="products">
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
                    <h1>Products Management</h1>
                    <p>Manage your jewellery inventory</p>
                </div>

                <!-- Products Content -->
                <div class="products-section">
                    <!-- Section Header with Button -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h2>Product Inventory</h2>
                            <p>Add, edit, and manage your jewellery products here.</p>
                        </div>
                        <button class="btn btn-primary" onclick="openProductModal()">
                            <i class="fas fa-plus"></i> Add New Product
                        </button>
                    </div>

                    <!-- Products Table -->
                    <div class="table-container">
                        <table class="data-table" id="productsTable">
                            <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                                            <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                            <p>No products found. Add your first product to get started.</p>
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

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Product</h2>
                <button class="modal-close" onclick="closeProductModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId" value="">
                    
                    <div class="form-group">
                        <label for="productName">Product Name *</label>
                        <input type="text" id="productName" name="productName" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Rings">Rings</option>
                                <option value="Necklaces">Necklaces</option>
                                <option value="Earrings">Earrings</option>
                                <option value="Bracelets">Bracelets</option>
                                <option value="Watches">Watches</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="In Stock">In Stock</option>
                                <option value="Out of Stock">Out of Stock</option>
                                <option value="Low Stock">Low Stock</option>
                                <option value="Discontinued">Discontinued</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock">Stock Quantity *</label>
                            <input type="number" id="stock" name="stock" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Price (UGX) *</label>
                            <input type="number" id="price" name="price" min="0" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Enter product description..."></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sku">SKU</label>
                            <input type="text" id="sku" name="sku" placeholder="Auto-generated if empty">
                        </div>
                        
                        <div class="form-group">
                            <label for="supplier">Supplier</label>
                            <select id="supplier" name="supplier">
                                <option value="">Select Supplier</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteProduct()" style="display: none;">Delete</button>
                <button type="button" class="btn btn-primary" onclick="saveProduct()">Save Product</button>
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
    <script src="../assets/js/app.js"></script>
    <script>
        const API_BASE = '../api/index.php?endpoint=products';
        const SUPPLIERS_API = '../api/index.php?endpoint=suppliers';
        let products = [];
        let suppliers = [];
        let currentEditId = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            loadSuppliers();
        });

        async function loadSuppliers() {
            try {
                const response = await fetch(SUPPLIERS_API);
                const data = await response.json();
                if (data.success) {
                    suppliers = data.data;
                    populateSupplierSelect();
                }
            } catch (error) {
                console.error('Error loading suppliers:', error);
            }
        }

        function populateSupplierSelect(selectedId = '') {
            const select = document.getElementById('supplier');
            if (!select) return;
            const current = select.value;
            select.innerHTML = '<option value=\"\">Select Supplier</option>' +
                suppliers.map(s => `<option value=\"${s.id}\" ${String(s.id) === String(selectedId || current) ? 'selected' : ''}>${s.name}</option>`).join('');
        }

        // Load products from database
        async function loadProducts() {
            try {
                const response = await fetch(API_BASE);
                const data = await response.json();
                if (data.success) {
                    products = data.data.map(p => ({
                        id: p.id,
                        name: p.name,
                        category: p.category_name || p.category_id,
                        stock: p.stock_quantity,
                        price: parseFloat(p.selling_price),
                        status: p.stock_quantity > 10 ? 'In Stock' : p.stock_quantity > 0 ? 'Low Stock' : 'Out of Stock',
                        description: p.description,
                        sku: p.sku,
                        supplier: p.supplier_name || p.supplier_id
                    }));
                    renderProductsTable();
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showNotification('Error loading products', 'error');
            }
        }

        // Render products table
        function renderProductsTable() {
            const tbody = document.querySelector('#productsTable tbody');
            
            if (products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">
                            <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                            <p>No products found. Add your first product to get started.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = products.map(product => `
                <tr>
                    <td>${product.name}</td>
                    <td>${product.category}</td>
                    <td>${product.stock}</td>
                    <td>UGX ${product.price.toFixed(2)}</td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; background: ${getStatusColor(product.status)}; color: white;">
                            ${product.status}
                        </span>
                    </td>
                    <td>
                        <button class="action-btn btn-edit" onclick="editProduct(${product.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn btn-delete" onclick="confirmDelete(${product.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Get status color
        function getStatusColor(status) {
            const colors = {
                'In Stock': '#28a745',
                'Out of Stock': '#dc3545',
                'Low Stock': '#ffc107',
                'Discontinued': '#6c757d'
            };
            return colors[status] || '#6c757d';
        }

        // Open product modal for adding
        function openProductModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('productForm').reset();
            document.getElementById('deleteBtn').style.display = 'none';
            document.getElementById('productModal').style.display = 'block';
        }

        // Open product modal for editing
        function editProduct(id) {
            currentEditId = id;
            const product = products.find(p => p.id === id);
            
            if (product) {
                document.getElementById('modalTitle').textContent = 'Edit Product';
                document.getElementById('productId').value = product.id;
                document.getElementById('productName').value = product.name;
                document.getElementById('category').value = product.category;
                document.getElementById('stock').value = product.stock;
                document.getElementById('price').value = product.price;
                document.getElementById('status').value = product.status;
                document.getElementById('description').value = product.description || '';
                document.getElementById('sku').value = product.sku || '';
                populateSupplierSelect(product.supplier);
                document.getElementById('deleteBtn').style.display = 'inline-block';
                document.getElementById('productModal').style.display = 'block';
            }
        }

        // Close product modal
        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
            document.getElementById('productForm').reset();
            currentEditId = null;
        }

        // Save product to database
        async function saveProduct() {
            const form = document.getElementById('productForm');
            const formData = new FormData(form);
            
            const productData = {
                name: formData.get('productName'),
                category_id: getCategoryId(formData.get('category')),
                stock: parseInt(formData.get('stock')),
                price: parseFloat(formData.get('price')),
                description: formData.get('description'),
                sku: formData.get('sku') || generateSKU(formData.get('category')),
                supplier_id: formData.get('supplier') || null
            };

            if (!productData.name || !formData.get('category')) {
                alert('Please fill in all required fields.');
                return;
            }

            try {
                const method = currentEditId ? 'PUT' : 'POST';
                if (currentEditId) productData.id = currentEditId;
                
                const response = await fetch(API_BASE, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadProducts();
                    closeProductModal();
                    showNotification(currentEditId ? 'Product updated!' : 'Product added!');
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving product:', error);
                alert('Error saving product');
            }
        }

        // Get category ID from name
        function getCategoryId(name) {
            const categories = { 'Rings': 1, 'Necklaces': 2, 'Earrings': 3, 'Bracelets': 4, 'Watches': 5, 'Other': 6 };
            return categories[name] || 1;
        }

        // Confirm delete
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                deleteProductById(id);
            }
        }

        // Delete product
        async function deleteProduct() {
            if (currentEditId && confirm('Are you sure?')) {
                await deleteProductById(currentEditId);
                closeProductModal();
            }
        }

        // Delete product by ID
        async function deleteProductById(id) {
            try {
                const response = await fetch(API_BASE + '&id=' + id, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                if (data.success) {
                    await loadProducts();
                    showNotification('Product deleted!');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('Error deleting product');
            }
        }

        // Generate SKU
        function generateSKU(category) {
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return category ? category.substring(0, 4).toUpperCase() + '-' + random : 'PROD-' + random;
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
            const modal = document.getElementById('productModal');
            if (event.target === modal) closeProductModal();
        }
    </script>
</body>
</html>
