// Configuration
const API_BASE_URL = '../controllers/ProductsAPI.php'; // Adjust path as needed
const VENDOR_API_URL = '../controllers/vendor-Dashboard.php'; // For categories
const VENDOR_ID = 1; // In production, get from session

// DOM Elements
const productViewModal = document.getElementById('view-product-modal');
const productModal = document.getElementById('product-modal');
const deleteModal = document.getElementById('delete-modal');
const closeModalBtns = document.querySelectorAll('.close-modal');
const cancelProductBtn = document.getElementById('cancel-product');
const cancelDeleteBtn = document.getElementById('cancel-delete');
const productForm = document.getElementById('product-form');
const addProductBtn = document.getElementById('add-product');
const refreshProductsBtn = document.getElementById('refresh-products');
const exportProductsBtn = document.getElementById('export-products');
const confirmDeleteBtn = document.getElementById('confirm-delete');
const gridViewBtn = document.getElementById('grid-view');
const listViewBtn = document.getElementById('list-view');
const productsGrid = document.getElementById('products-grid');
const productsTable = document.getElementById('products-table');
const productsTableBody = document.getElementById('products-table-body');
const imageUpload = document.getElementById('image-upload');
const productImageInput = document.getElementById('product-image');
const imagePreview = document.getElementById('image-preview');
const filtersContent = document.getElementById('filters-content');
const toggleFiltersBtn = document.getElementById('toggle-filters');
const applyFiltersBtn = document.getElementById('apply-filters');
const resetFiltersBtn = document.getElementById('reset-filters');
const bulkActions = document.getElementById('bulk-actions');
const selectAllCheckbox = document.getElementById('select-all-table');

// Current vendor data
let currentVendor = {
    id: VENDOR_ID,
    name: "TechGadgets Inc.",
    email: "contact@techgadgets.com"
};

// Current view state
let currentView = 'grid';
let currentProducts = [];
let productToDelete = null;
let editingProductId = null;

// Categories cache
let categoriesCache = [];

// Initialize the products page
function initProductsPage() {
    loadVendorData();
    loadCategories(); // Load categories on init
    setupEventListeners();
    loadProductsData();
}

// Setup event listeners
function setupEventListeners() {
    // Navigation
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', (e) => {
            if (!link.getAttribute('href') || link.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    });
    
    // Buttons
    addProductBtn.addEventListener('click', () => openProductModal());
    refreshProductsBtn.addEventListener('click', loadProductsData);
    exportProductsBtn.addEventListener('click', exportProducts);
    
    // View toggle
    gridViewBtn.addEventListener('click', () => switchView('grid'));
    listViewBtn.addEventListener('click', () => switchView('list'));
    
    // Modals
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModals);
    });
    
    cancelProductBtn.addEventListener('click', closeModals);
    cancelDeleteBtn.addEventListener('click', closeModals);
    
    productForm.addEventListener('submit', saveProduct);
    confirmDeleteBtn.addEventListener('click', deleteProduct);
    
    // Image upload
    imageUpload.addEventListener('click', () => productImageInput.click());
    productImageInput.addEventListener('change', handleImageUpload);
    
    // Filters
    toggleFiltersBtn.addEventListener('click', toggleFilters);
    applyFiltersBtn.addEventListener('click', applyFilters);
    resetFiltersBtn.addEventListener('click', resetFilters);
    
    // Bulk actions
    selectAllCheckbox.addEventListener('change', handleSelectAll);
    document.getElementById('apply-bulk-action').addEventListener('click', applyBulkAction);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === productModal) closeModals();
        if (e.target === deleteModal) closeModals();
    });
}

// Load vendor data
function loadVendorData() {
    document.getElementById('vendor-name').textContent = currentVendor.name;
    document.getElementById('vendor-email').textContent = currentVendor.email;
}

// Load categories from API
async function loadCategories() {
    try {
        const response = await fetch(`${VENDOR_API_URL}?action=get_categories`);
        const data = await response.json();
        
        if (data.error) throw new Error(data.error);
        
        categoriesCache = data;
        
        // Populate category filters
        populateCategoryFilters();
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

// Populate category filter dropdowns
function populateCategoryFilters() {
    const categoryFilter = document.getElementById('category-filter');
    
    if (categoryFilter) {
        // Keep the "All Categories" option
        const allOption = categoryFilter.querySelector('option[value="all"]');
        categoryFilter.innerHTML = '';
        if (allOption) {
            categoryFilter.appendChild(allOption);
        } else {
            const newAllOption = document.createElement('option');
            newAllOption.value = 'all';
            newAllOption.textContent = 'All Categories';
            categoryFilter.appendChild(newAllOption);
        }
        
        // Add categories from cache
        categoriesCache.forEach(category => {
            const option = document.createElement('option');
            option.value = category.category_id;
            option.textContent = category.category_name;
            categoryFilter.appendChild(option);
        });
    }
}

// Populate category dropdown in product form
function populateProductCategoryDropdown(selectedCategoryId = null) {
    const categorySelect = document.getElementById('product-category');
    
    if (!categorySelect) return;
    
    // Clear existing options
    categorySelect.innerHTML = '<option value="">Select Category</option>';
    
    // Add categories from cache
    categoriesCache.forEach(category => {
        const option = document.createElement('option');
        option.value = category.category_id;
        option.textContent = category.category_name;
        
        // Set selected if this matches the current product's category
        if (selectedCategoryId && category.category_id == selectedCategoryId) {
            option.selected = true;
        }
        
        categorySelect.appendChild(option);
    });
}

// Load products data
async function loadProductsData() {
    try {
        showLoading();
        const products = await getProductsData();
        currentProducts = products;
        renderProducts(products);
        hideLoading();
    } catch (error) {
        console.error('Error loading products:', error);
        hideLoading();
        showError('Error loading products. Please try again.');
    }
}

// Switch between grid and list view
function switchView(view) {
    currentView = view;
    
    if (view === 'grid') {
        gridViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        productsGrid.style.display = 'grid';
        productsTable.style.display = 'none';
    } else {
        gridViewBtn.classList.remove('active');
        listViewBtn.classList.add('active');
        productsGrid.style.display = 'none';
        productsTable.style.display = 'table';
    }
}

// Render products based on current view
function renderProducts(products) {
    if (currentView === 'grid') {
        renderProductsGrid(products);
    } else {
        renderProductsTable(products);
    }
    
    // Update pagination info
    document.getElementById('pagination-info').textContent = `Showing 1-${products.length} of ${products.length} products`;
}

// Render products in grid view
function renderProductsGrid(products) {
    productsGrid.innerHTML = '';
    
    if (products.length === 0) {
        productsGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <i class="fas fa-box-open" style="font-size: 3rem; color: var(--light-gray); margin-bottom: 15px;"></i>
                <h3 style="color: var(--gray); margin-bottom: 10px;">No products found</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">Try adjusting your filters or add a new product.</p>
                <button class="btn btn-primary" id="add-product-empty">
                    <i class="fas fa-plus"></i> Add Your First Product
                </button>
            </div>
        `;
        
        document.getElementById('add-product-empty').addEventListener('click', () => openProductModal());
        return;
    }
    
    products.forEach(product => {
        const stockClass = product.stock < 10 ? 'stock-low' : 
                         product.stock < 50 ? 'stock-medium' : 'stock-high';
        const statusClass = product.status === 'active' ? 'status-active' : 'status-inactive';
        
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <img src="${product.image}" alt="${product.name}" class="product-image">
            <div class="product-info">
                <div class="product-header">
                    <div>
                        <div class="product-name">${escapeHtml(product.name)}</div>
                        <div class="product-sku">${product.sku}</div>
                    </div>
                    <div class="product-price">$${product.price}</div>
                </div>
                <div class="product-category">${escapeHtml(product.category)}</div>
                <p class="product-description">${escapeHtml(product.description)}</p>
                <div class="product-stats">
                    <div class="stat">
                        <div class="stat-value">${product.stock}</div>
                        <div class="stat-label">In Stock</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">${product.sales}</div>
                        <div class="stat-label">Sold</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">${product.rating}</div>
                        <div class="stat-label">Rating</div>
                    </div>
                </div>
                <div class="product-actions">
                    <button class="btn btn-primary btn-sm edit-product" data-id="${product.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-outline btn-sm view-product" data-id="${product.id}">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-danger btn-sm delete-product" data-id="${product.id}" data-name="${escapeHtml(product.name)}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        productsGrid.appendChild(productCard);
    });
    
    // Add event listeners to action buttons
    attachProductActionListeners();
}

// Render products in table view
function renderProductsTable(products) {
    productsTableBody.innerHTML = '';
    
    if (products.length === 0) {
        productsTableBody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <i class="fas fa-box-open" style="font-size: 2rem; color: var(--light-gray); margin-bottom: 15px;"></i>
                    <div style="color: var(--gray);">No products found</div>
                </td>
            </tr>
        `;
        return;
    }
    
    products.forEach(product => {
        const stockClass = product.stock < 10 ? 'stock-low' : 
                         product.stock < 50 ? 'stock-medium' : 'stock-high';
        const statusClass = product.status === 'active' ? 'status-active' : 'status-inactive';
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="checkbox" class="product-checkbox" data-id="${product.id}"></td>
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                    <div>
                        <div style="font-weight: 500;">${escapeHtml(product.name)}</div>
                        <div style="font-size: 0.8rem; color: var(--gray);">${product.sku}</div>
                    </div>
                </div>
            </td>
            <td>${escapeHtml(product.category)}</td>
            <td>$${product.price}</td>
            <td>
                <span class="status-badge ${stockClass}">${product.stock}</span>
            </td>
            <td>
                <span class="status-badge ${statusClass}">${product.status}</span>
            </td>
            <td>${product.sales}</td>
            <td>
                <button class="btn btn-primary btn-sm edit-product" data-id="${product.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline btn-sm view-product" data-id="${product.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-danger btn-sm delete-product" data-id="${product.id}" data-name="${escapeHtml(product.name)}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        productsTableBody.appendChild(row);
    });
    
    // Add event listeners
    attachProductActionListeners();
    
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
}

// Attach event listeners to product action buttons
function attachProductActionListeners() {
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.target.closest('button').getAttribute('data-id');
            openProductModal(productId);
        });
    });
    
    document.querySelectorAll('.view-product').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.target.closest('button').getAttribute('data-id');
            viewProductDetails(productId);
        });
    });
    
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.target.closest('button').getAttribute('data-id');
            const productName = e.target.closest('button').getAttribute('data-name');
            confirmDelete(productId, productName);
        });
    });
}

// Open product modal (for add/edit)
async function openProductModal(productId = null) {
    const modalTitle = document.getElementById('product-modal-title');
    editingProductId = productId;
    
    if (productId) {
        modalTitle.textContent = 'Edit Product';
        try {
            const product = await getProductById(productId);
            
            document.getElementById('product-name').value = product.name;
            document.getElementById('product-sku').value = product.sku;
            document.getElementById('product-price').value = product.price;
            document.getElementById('product-stock').value = product.stock;
            document.getElementById('product-status').value = product.status;
            document.getElementById('product-description').value = product.description || '';
            
            // Populate category dropdown with selected value
            populateProductCategoryDropdown(product.category_id);
            
            if (product.image) {
                imagePreview.src = product.image;
                imagePreview.style.display = 'block';
            }
        } catch (error) {
            showError('Failed to load product details');
            return;
        }
    } else {
        modalTitle.textContent = 'Add New Product';
        editingProductId = null;
        document.getElementById('product-form').reset();
        imagePreview.style.display = 'none';
        
        // Populate category dropdown for new product
        populateProductCategoryDropdown();
    }
    
    productModal.style.display = 'flex';
}

// View product details
async function viewProductDetails(productId) {
    try {
        const product = await getProductById(productId);
        alert(`Product Details:\n\nName: ${product.name}\nSKU: ${product.sku}\nPrice: $${product.price}\nStock: ${product.stock}\nCategory: ${product.category}\nStatus: ${product.status}`);
    } catch (error) {
        showError('Failed to load product details');
    }
}

// Confirm product deletion
function confirmDelete(productId, productName) {
    productToDelete = productId;
    document.getElementById('delete-product-name').textContent = productName;
    deleteModal.style.display = 'flex';
}

// Close all modals
function closeModals() {
    productModal.style.display = 'none';
    deleteModal.style.display = 'none';
    editingProductId = null;
}

// Handle image upload
function handleImageUpload(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Save product (add or edit)
async function saveProduct(e) {
    e.preventDefault();
    
    const productData = {
        name: document.getElementById('product-name').value,
        category_id: document.getElementById('product-category').value,
        price: parseFloat(document.getElementById('product-price').value),
        stock: parseInt(document.getElementById('product-stock').value),
        status: document.getElementById('product-status').value,
        description: document.getElementById('product-description').value,
        image_url: imagePreview.style.display === 'block' ? imagePreview.src : null
    };
    
    // Validate form
    if (!productData.name || !productData.category_id || !productData.price || productData.stock < 0) {
        showError('Please fill in all required fields correctly.');
        return;
    }
    
    // Add ID if editing
    if (editingProductId) {
        productData.id = editingProductId;
    }
    
    try {
        showLoading();
        await saveProductAPI(productData);
        hideLoading();
        showSuccess('Product saved successfully!');
        closeModals();
        loadProductsData();
    } catch (error) {
        hideLoading();
        showError('Error saving product: ' + error.message);
    }
}

// Delete product
async function deleteProduct() {
    if (!productToDelete) return;
    
    try {
        showLoading();
        await deleteProductAPI(productToDelete);
        hideLoading();
        showSuccess('Product deleted successfully!');
        closeModals();
        loadProductsData();
        productToDelete = null;
    } catch (error) {
        hideLoading();
        showError('Error deleting product: ' + error.message);
    }
}

// Toggle filters visibility
function toggleFilters() {
    if (filtersContent.style.display === 'none' || !filtersContent.style.display) {
        filtersContent.style.display = 'grid';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Hide Filters';
    } else {
        filtersContent.style.display = 'none';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Show Filters';
    }
}

// Apply filters
function applyFilters() {
    loadProductsData();
    
    // Close filters on mobile
    if (window.innerWidth < 768) {
        filtersContent.style.display = 'none';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Show Filters';
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('category-filter').value = 'all';
    document.getElementById('status-filter').value = 'all';
    document.getElementById('stock-filter').value = 'all';
    document.getElementById('search-products').value = '';
    loadProductsData();
}

// Handle select all checkboxes
function handleSelectAll(e) {
    const isChecked = e.target.checked;
    
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    
    updateBulkActions();
}

// Update bulk actions based on selected items
function updateBulkActions() {
    const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
    
    if (selectedCount > 0) {
        bulkActions.style.display = 'flex';
        document.getElementById('selected-count').textContent = `${selectedCount} products selected`;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Apply bulk action
async function applyBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const selectedProducts = [];
    
    document.querySelectorAll('.product-checkbox:checked').forEach(checkbox => {
        selectedProducts.push(parseInt(checkbox.getAttribute('data-id')));
    });
    
    if (action && selectedProducts.length > 0) {
        try {
            showLoading();
            await bulkUpdateProductsAPI(selectedProducts, action);
            hideLoading();
            showSuccess(`Bulk action "${action}" applied to ${selectedProducts.length} products.`);
            
            // Reset selection
            document.querySelectorAll('.product-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAllCheckbox.checked = false;
            bulkActions.style.display = 'none';
            
            loadProductsData();
        } catch (error) {
            hideLoading();
            showError('Error applying bulk action: ' + error.message);
        }
    } else {
        showError('Please select an action and at least one product.');
    }
}

// Export products
function exportProducts() {
    // Create CSV content
    let csv = 'Name,SKU,Category,Price,Stock,Status,Sales\n';
    
    currentProducts.forEach(product => {
        csv += `"${product.name}","${product.sku}","${product.category}",${product.price},${product.stock},"${product.status}",${product.sales}\n`;
    });
    
    // Create download link
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `products_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    showSuccess('Products exported successfully!');
}

// API Functions
async function getProductsData() {
    const category = document.getElementById('category-filter')?.value || 'all';
    const status = document.getElementById('status-filter')?.value || 'all';
    const stock = document.getElementById('stock-filter')?.value || 'all';
    const search = document.getElementById('search-products')?.value || '';
    
    const params = new URLSearchParams({
        action: 'list',
        vendor_id: VENDOR_ID,
        category: category,
        status: status,
        stock: stock,
        search: search
    });
    
    const response = await fetch(`${API_BASE_URL}?${params}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.ok) {
        throw new Error('Failed to fetch products');
    }
    
    const result = await response.json();
    return result.data || [];
}

async function getProductById(productId) {
    const params = new URLSearchParams({
        action: 'single',
        id: productId,
        vendor_id: VENDOR_ID
    });
    
    const response = await fetch(`${API_BASE_URL}?${params}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.ok) {
        throw new Error('Failed to fetch product');
    }
    
    const result = await response.json();
    return result.data;
}

async function saveProductAPI(productData) {
    const isUpdate = !!productData.id;
    const url = isUpdate ? `${API_BASE_URL}?vendor_id=${VENDOR_ID}` : `${API_BASE_URL}?action=create&vendor_id=${VENDOR_ID}`;
    const method = isUpdate ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(productData)
    });
    
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to save product');
    }
    
    return await response.json();
}

async function deleteProductAPI(productId) {
    const response = await fetch(`${API_BASE_URL}?id=${productId}&vendor_id=${VENDOR_ID}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to delete product');
    }
    
    return await response.json();
}

async function bulkUpdateProductsAPI(productIds, action) {
    const response = await fetch(`${API_BASE_URL}?action=bulk&vendor_id=${VENDOR_ID}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_ids: productIds,
            action: action
        })
    });
    
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to perform bulk action');
    }
    
    return await response.json();
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showLoading() {
    document.body.style.cursor = 'wait';
}

function hideLoading() {
    document.body.style.cursor = 'default';
}

function showSuccess(message) {
    alert(message); // Replace with a better notification system
}

function showError(message) {
    alert(message); // Replace with a better notification system
}

// Initialize the page when loaded
document.addEventListener('DOMContentLoaded', initProductsPage);