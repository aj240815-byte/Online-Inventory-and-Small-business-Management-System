/**
 * Products Page Functions
 * JIMS - Jewellery Inventory Management System
 */

// Products Functions
async function loadProducts() {
    try {
        showLoading();
        const data = await apiRequest('products.php');
        renderProducts(data);
    } catch (error) {
        console.error('Failed to load products:', error);
        renderProductsError();
    } finally {
        hideLoading();
    }
}

function renderProducts(data) {
    const html = `
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Products</h3>
                <div>
                    <button class="btn btn-primary" onclick="showAddProductModal()">Add Product</button>
                </div>
            </div>
            <div class="card-body">
                <div class="search-bar">
                    <input type="text" class="form-control search-input" placeholder="Search products..." id="productSearch">
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                    <button class="btn btn-outline" onclick="searchProducts()">Search</button>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.products.map(product => `
                                <tr>
                                    <td>${product.sku}</td>
                                    <td>${product.name}</td>
                                    <td>${product.category_name || 'N/A'}</td>
                                    <td>
                                        <span class="badge badge-${getStockStatusClass(product.stock_quantity, product.reorder_level)}">
                                            ${product.stock_quantity}
                                        </span>
                                    </td>
                                    <td>$${parseFloat(product.cost_price).toFixed(2)}</td>
                                    <td>$${parseFloat(product.selling_price).toFixed(2)}</td>
                                    <td>
                                        <span class="badge badge-${product.is_active ? 'success' : 'secondary'}">
                                            ${product.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="btn btn-sm btn-primary" onclick="editProduct(${product.id})">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
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
    loadCategories();
}

function renderProductsError() {
    contentArea.innerHTML = `
        <div class="card">
            <div class="card-body text-center">
                <h3>Products</h3>
                <p>Unable to load products data. Please check your database connection.</p>
                <button class="btn btn-primary" onclick="loadProducts()">Retry</button>
            </div>
        </div>
    `;
}

function showAddProductModal() {
    showModal('Add Product', `
        <form id="productForm">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SKU *</label>
                    <input type="text" class="form-control" id="productSku" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" class="form-control" id="productName" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control form-textarea" id="productDescription"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select class="form-select" id="productCategory">
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <select class="form-select" id="productSupplier">
                        <option value="">Select Supplier</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Cost Price *</label>
                    <input type="number" class="form-control" id="productCostPrice" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" class="form-control" id="productSellingPrice" step="0.01" min="0" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" id="productStock" min="0" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Reorder Level</label>
                    <input type="number" class="form-control" id="productReorderLevel" min="0" value="5">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Material Type</label>
                <input type="text" class="form-control" id="productMaterial" placeholder="e.g., Gold, Silver">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Weight (g)</label>
                    <input type="number" class="form-control" id="productWeight" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Carat</label>
                    <input type="number" class="form-control" id="productCarat" step="0.01" min="0">
                </div>
            </div>
        </form>
    `, `
        <button class="btn btn-primary" onclick="saveProduct()">Save Product</button>
        <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
    `);
    
    // Load categories and suppliers for the form
    loadCategoriesForForm();
    loadSuppliersForForm();
}

async function saveProduct() {
    const productData = {
        sku: document.getElementById('productSku').value,
        name: document.getElementById('productName').value,
        description: document.getElementById('productDescription').value,
        category_id: document.getElementById('productCategory').value,
        supplier_id: document.getElementById('productSupplier').value,
        cost_price: parseFloat(document.getElementById('productCostPrice').value),
        selling_price: parseFloat(document.getElementById('productSellingPrice').value),
        stock_quantity: parseInt(document.getElementById('productStock').value),
        reorder_level: parseInt(document.getElementById('productReorderLevel').value),
        material_type: document.getElementById('productMaterial').value,
        weight: parseFloat(document.getElementById('productWeight').value),
        carat: parseFloat(document.getElementById('productCarat').value)
    };
    
    // Validate required fields
    if (!productData.sku || !productData.name || !productData.cost_price || !productData.selling_price) {
        showError('Please fill in all required fields');
        return;
    }
    
    try {
        await apiRequest('products.php', {
            method: 'POST',
            body: productData
        });
        
        closeModal('dynamicModal');
        showSuccess('Product added successfully!');
        loadProducts();
        
    } catch (error) {
        showError('Failed to add product: ' + error.message);
    }
}

function editProduct(productId) {
    console.log('Edit product:', productId);
    // Load product data and show edit modal
    showEditProductModal(productId);
}

async function showEditProductModal(productId) {
    try {
        const product = await apiRequest(`products.php?id=${productId}`);
        
        showModal('Edit Product', `
            <form id="editProductForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">SKU *</label>
                        <input type="text" class="form-control" id="editProductSku" value="${product.sku}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" id="editProductName" value="${product.name}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control form-textarea" id="editProductDescription">${product.description}</textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Cost Price *</label>
                        <input type="number" class="form-control" id="editProductCostPrice" value="${product.cost_price}" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selling Price *</label>
                        <input type="number" class="form-control" id="editProductSellingPrice" value="${product.selling_price}" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="editProductStock" value="${product.stock_quantity}" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reorder Level</label>
                        <input type="number" class="form-control" id="editProductReorderLevel" value="${product.reorder_level}" min="0">
                    </div>
                </div>
            </form>
        `, `
            <button class="btn btn-primary" onclick="updateProduct(${productId})">Update Product</button>
            <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
        `);
        
    } catch (error) {
        showError('Failed to load product data: ' + error.message);
    }
}

async function updateProduct(productId) {
    const productData = {
        sku: document.getElementById('editProductSku').value,
        name: document.getElementById('editProductName').value,
        description: document.getElementById('editProductDescription').value,
        cost_price: parseFloat(document.getElementById('editProductCostPrice').value),
        selling_price: parseFloat(document.getElementById('editProductSellingPrice').value),
        stock_quantity: parseInt(document.getElementById('editProductStock').value),
        reorder_level: parseInt(document.getElementById('editProductReorderLevel').value)
    };
    
    try {
        await apiRequest(`products.php?id=${productId}`, {
            method: 'PUT',
            body: productData
        });
        
        closeModal('dynamicModal');
        showSuccess('Product updated successfully!');
        loadProducts();
        
    } catch (error) {
        showError('Failed to update product: ' + error.message);
    }
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        apiRequest(`products.php?id=${productId}`, { method: 'DELETE' })
            .then(() => {
                showSuccess('Product deleted successfully!');
                loadProducts();
            })
            .catch(error => {
                showError('Failed to delete product: ' + error.message);
            });
    }
}

function searchProducts() {
    const searchTerm = document.getElementById('productSearch').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    
    // Build query parameters
    const params = new URLSearchParams();
    if (searchTerm) params.append('search', searchTerm);
    if (categoryFilter) params.append('category', categoryFilter);
    
    const url = `products.php${params.toString() ? '?' + params.toString() : ''}`;
    
    apiRequest(url)
        .then(data => {
            renderProducts(data);
        })
        .catch(error => {
            showError('Failed to search products: ' + error.message);
        });
}

async function loadCategories() {
    try {
        const data = await apiRequest('api/categories.php');
        const categoryFilter = document.getElementById('categoryFilter');
        
        if (categoryFilter) {
            data.categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categoryFilter.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Failed to load categories:', error);
    }
}

async function loadCategoriesForForm() {
    try {
        const data = await apiRequest('api/categories.php');
        const categorySelect = document.getElementById('productCategory');
        
        if (categorySelect) {
            data.categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Failed to load categories for form:', error);
    }
}

async function loadSuppliersForForm() {
    try {
        const data = await apiRequest('suppliers.php');
        const supplierSelect = document.getElementById('productSupplier');
        
        if (supplierSelect) {
            data.suppliers.forEach(supplier => {
                const option = document.createElement('option');
                option.value = supplier.id;
                option.textContent = supplier.name;
                supplierSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Failed to load suppliers for form:', error);
    }
}
