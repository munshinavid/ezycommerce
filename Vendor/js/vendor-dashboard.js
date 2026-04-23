// Configuration
const API_BASE_URL = '../controllers/vendor-Dashboard.php';
const VENDOR_ID = 1; // In production, get from session/auth

// DOM Elements
const productModal = document.getElementById('product-modal');
const stockModal = document.getElementById('stock-modal');
const closeModalBtns = document.querySelectorAll('.close-modal');
const cancelProductBtn = document.getElementById('cancel-product');
const cancelStockBtn = document.getElementById('cancel-stock');
const productForm = document.getElementById('product-form');
const stockForm = document.getElementById('stock-form');
const addProductBtn = document.getElementById('add-product');
const refreshDataBtn = document.getElementById('refresh-data');
const refreshProductsBtn = document.getElementById('refresh-products');
const manageStockBtn = document.getElementById('manage-stock');

// Image Upload Elements
const fileInput = document.getElementById('product-image');
const browseBtn = document.getElementById('browse-btn');
const removeBtn = document.getElementById('remove-image');
const imagePreview = document.getElementById('image-preview');
const uploadContainer = document.getElementById('image-upload-container');

// Charts
let salesChart, productsChart;

// Current vendor data
let currentVendor = {
    id: VENDOR_ID,
    name: "Loading...",
    email: "Loading..."
};

// Current product being edited
let currentProductId = null;
let currentProductData = null;

// Categories cache
let categoriesCache = [];

// Initialize the dashboard
function initVendorDashboard() {
    loadVendorData();
    loadCategories(); // Load categories on init
    setupEventListeners();
    initializeCharts();
    loadDashboardData();
    setupImageUpload();
}

// Setup event listeners
function setupEventListeners() {
    // Navigation
    document.getElementById('nav-products')?.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Products page would load here');
    });
    
    document.getElementById('nav-orders')?.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Orders page would load here');
    });
    
    document.getElementById('nav-sales')?.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Sales analytics page would load here');
    });
    
    document.getElementById('nav-returns')?.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Returns page would load here');
    });
    
    document.getElementById('nav-discounts')?.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Discounts page would load here');
    });
    
    document.getElementById('nav-profile')?.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Profile page would load here');
    });
    
    // Buttons
    addProductBtn.addEventListener('click', () => openProductModal());
    refreshDataBtn.addEventListener('click', loadDashboardData);
    refreshProductsBtn.addEventListener('click', loadProductsData);
    manageStockBtn?.addEventListener('click', () => {
        alert('Stock management page would load here');
    });
    
    document.getElementById('view-all-orders')?.addEventListener('click', () => {
        alert('All orders page would load here');
    });
    
    document.getElementById('export-products')?.addEventListener('click', exportProducts);
    
    // Modals
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModals);
    });
    
    cancelProductBtn.addEventListener('click', closeModals);
    cancelStockBtn.addEventListener('click', closeModals);
    
    productForm.addEventListener('submit', saveProduct);
    stockForm.addEventListener('submit', updateStock);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === productModal) closeModals();
        if (e.target === stockModal) closeModals();
    });
}

// Setup image upload functionality
function setupImageUpload() {
    browseBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileSelect);
    removeBtn.addEventListener('click', removeImage);

    uploadContainer.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadContainer.classList.add('dragover');
    });

    uploadContainer.addEventListener('dragleave', () => {
        uploadContainer.classList.remove('dragover');
    });

    uploadContainer.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadContainer.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });
}

function handleFileSelect() {
    const file = fileInput.files[0];
    
    if (file) {
        if (!file.type.match('image.*')) {
            alert('Please select an image file (JPG, PNG, GIF)');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }

        const reader = new FileReader();
        
        reader.onload = function(e) {
            imagePreview.innerHTML = '';
            const img = document.createElement('img');
            img.src = e.target.result;
            imagePreview.appendChild(img);
            removeBtn.style.display = 'inline-flex';
        };
        
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    fileInput.value = '';
    imagePreview.innerHTML = '<i class="fas fa-image"></i><span>No image selected</span>';
    removeBtn.style.display = 'none';
}

// Load vendor data from API
function loadVendorData() {
    fetch(`${API_BASE_URL}?action=get_vendor_info&vendor_id=${VENDOR_ID}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            
            currentVendor = {
                id: data.vendor_id,
                name: data.name,
                email: data.email
            };
            document.getElementById('vendor-name').textContent = currentVendor.name;
            document.getElementById('vendor-email').textContent = currentVendor.email;
        })
        .catch(error => {
            console.error('Error loading vendor data:', error);
        });
}

// Load categories from API
function loadCategories() {
    fetch(`${API_BASE_URL}?action=get_categories`)
        .then(response => response.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            
            categoriesCache = data;
            populateCategoryDropdown();
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

// Populate category dropdown
function populateCategoryDropdown(selectedCategoryId = null) {
    const categorySelect = document.getElementById('product-category');
    categorySelect.innerHTML = '<option value="">Select Category</option>';
    
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

// Load dashboard data from API
function loadDashboardData() {
    Promise.all([
        fetch(`${API_BASE_URL}?action=get_stats&vendor_id=${VENDOR_ID}`).then(r => r.json()),
        fetch(`${API_BASE_URL}?action=get_recent_orders&vendor_id=${VENDOR_ID}`).then(r => r.json()),
        fetch(`${API_BASE_URL}?action=get_low_stock&vendor_id=${VENDOR_ID}`).then(r => r.json()),
        fetch(`${API_BASE_URL}?action=get_products&vendor_id=${VENDOR_ID}`).then(r => r.json())
    ]).then(([stats, orders, lowStock, products]) => {
        updateDashboardCards(stats);
        renderRecentOrders(orders);
        renderLowStockProducts(lowStock);
        renderProducts(products);
        updateCharts(stats);
    }).catch(error => {
        console.error('Error loading dashboard data:', error);
        alert('Error loading dashboard data. Please try again.');
    });
}

// Load products data
function loadProductsData() {
    fetch(`${API_BASE_URL}?action=get_products&vendor_id=${VENDOR_ID}`)
        .then(response => response.json())
        .then(products => {
            renderProducts(products);
            alert('Products data refreshed!');
        })
        .catch(error => {
            console.error('Error loading products:', error);
            alert('Error loading products. Please try again.');
        });
}

// Update dashboard cards with stats
function updateDashboardCards(stats) {
    document.getElementById('total-products').textContent = stats.totalProducts;
    document.getElementById('pending-orders').textContent = stats.pendingOrders;
    document.getElementById('monthly-revenue').textContent = `$${stats.monthlyRevenue}`;
    document.getElementById('low-stock').textContent = stats.lowStockItems;
}

// Render recent orders table
function renderRecentOrders(orders) {
    const ordersBody = document.getElementById('recent-orders-body');
    ordersBody.innerHTML = '';
    
    if (orders.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="5" style="text-align: center;">No recent orders found</td>`;
        ordersBody.appendChild(row);
        return;
    }
    
    orders.forEach(order => {
        const statusClass = `status-${order.status.toLowerCase()}`;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${order.id}</td>
            <td>${order.customer}</td>
            <td>${formatDate(order.date)}</td>
            <td>$${order.amount}</td>
            <td><span class="status-badge ${statusClass}">${order.status}</span></td>
        `;
        ordersBody.appendChild(row);
    });
}

// Render low stock products table
function renderLowStockProducts(products) {
    const lowStockBody = document.getElementById('low-stock-body');
    lowStockBody.innerHTML = '';
    
    if (products.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="5" style="text-align: center;">No low stock products</td>`;
        lowStockBody.appendChild(row);
        return;
    }
    
    products.forEach(product => {
        const stockClass = product.stock < 10 ? 'stock-low' : 'stock-medium';
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${product.name}</td>
            <td>${product.sku}</td>
            <td>${product.stock}</td>
            <td><span class="status-badge ${stockClass}">Low Stock</span></td>
            <td>
                <button class="btn btn-primary btn-sm update-stock-btn" data-id="${product.id}" data-name="${product.name}" data-stock="${product.stock}">
                    <i class="fas fa-edit"></i> Update
                </button>
            </td>
        `;
        lowStockBody.appendChild(row);
    });
    
    document.querySelectorAll('.update-stock-btn').forEach(button => {
        button.addEventListener('click', openStockModal);
    });
}

// Render products table
function renderProducts(products) {
    const productsBody = document.getElementById('products-body');
    productsBody.innerHTML = '';
    
    if (products.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="6" style="text-align: center;">No products found</td>`;
        productsBody.appendChild(row);
        return;
    }
    
    products.forEach(product => {
        const stockClass = product.stock < 10 ? 'stock-low' : 
                         product.stock < 50 ? 'stock-medium' : 'stock-high';
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                    <div>
                        <div style="font-weight: 500;">${product.name}</div>
                        <div style="font-size: 0.8rem; color: var(--gray);">${product.sku}</div>
                    </div>
                </div>
            </td>
            <td>${product.category}</td>
            <td>$${product.price}</td>
            <td>${product.stock}</td>
            <td>
                <span class="status-badge ${stockClass}">${stockClass.replace('stock-', '').toUpperCase()} Stock</span>
            </td>
            <td>
                <button class="btn btn-primary btn-sm edit-product-btn" data-id="${product.id}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-outline btn-sm update-stock-btn" data-id="${product.id}" data-name="${product.name}" data-stock="${product.stock}">
                    <i class="fas fa-box"></i> Stock
                </button>
            </td>
        `;
        productsBody.appendChild(row);
    });
    
    document.querySelectorAll('.edit-product-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.target.closest('button').getAttribute('data-id');
            openProductModal(productId);
        });
    });
    
    document.querySelectorAll('.update-stock-btn').forEach(button => {
        button.addEventListener('click', openStockModal);
    });
}

// Open product modal (for add/edit)
function openProductModal(productId = null) {
    const modalTitle = document.getElementById('product-modal-title');
    
    if (productId) {
        modalTitle.textContent = 'Edit Product';
        currentProductId = productId;
        
        // Fetch product details
        fetch(`${API_BASE_URL}?action=get_product&vendor_id=${VENDOR_ID}&product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                
                currentProductData = data;
                
                // Populate form
                document.getElementById('product-name').value = data.name;
                document.getElementById('product-description').value = data.description || '';
                document.getElementById('product-price').value = data.price;
                document.getElementById('product-stock').value = data.stock;
                
                // Populate category dropdown with selected value
                populateCategoryDropdown(data.category_id);
                
                // Show existing image
                if (data.image_url) {
                    imagePreview.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = data.image_url;
                    imagePreview.appendChild(img);
                    removeBtn.style.display = 'inline-flex';
                }
                
                productModal.style.display = 'flex';
            })
            .catch(error => {
                alert('Error loading product: ' + error.message);
            });
    } else {
        modalTitle.textContent = 'Add New Product';
        currentProductId = null;
        currentProductData = null;
        document.getElementById('product-form').reset();
        removeImage();
        
        // Populate category dropdown for new product
        populateCategoryDropdown();
        
        productModal.style.display = 'flex';
    }
}

// Open stock update modal
function openStockModal(e) {
    const productId = e.target.closest('button').getAttribute('data-id');
    const productName = e.target.closest('button').getAttribute('data-name');
    const currentStock = e.target.closest('button').getAttribute('data-stock');
    
    document.getElementById('stock-product-id').value = productId;
    document.getElementById('stock-product-name').textContent = productName;
    document.getElementById('current-stock').value = currentStock;
    document.getElementById('stock-quantity').value = '';
    document.getElementById('stock-reason').value = '';
    
    stockModal.style.display = 'flex';
}

// Close all modals
function closeModals() {
    productModal.style.display = 'none';
    stockModal.style.display = 'none';
    currentProductId = null;
    currentProductData = null;
}

// Save product (add or edit)
function saveProduct(e) {
    e.preventDefault();
    
    const formData = new FormData();
    
    // Add product ID if editing
    if (currentProductId) {
        formData.append('product_id', currentProductId);
    }
    
    // Add form fields
    formData.append('name', document.getElementById('product-name').value);
    formData.append('category_id', document.getElementById('product-category').value);
    formData.append('description', document.getElementById('product-description').value);
    formData.append('price', document.getElementById('product-price').value);
    formData.append('stock', document.getElementById('product-stock').value);
    
    // Add image only if a new one was selected
    const file = fileInput.files[0];
    if (file) {
        formData.append('image', file);
    } else if (!currentProductId) {
        // New product requires image
        alert('Please select a product image.');
        return;
    }
    
    // Send to API
    fetch(`${API_BASE_URL}?action=save_product&vendor_id=${VENDOR_ID}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            throw new Error(result.error);
        }
        
        alert(result.message);
        closeModals();
        loadDashboardData();
    })
    .catch(error => {
        alert('Error saving product: ' + error.message);
    });
}

// Update stock
function updateStock(e) {
    e.preventDefault();
    
    const productId = document.getElementById('stock-product-id').value;
    const currentStock = parseInt(document.getElementById('current-stock').value);
    const action = document.getElementById('stock-action').value;
    const quantity = parseInt(document.getElementById('stock-quantity').value);
    const reason = document.getElementById('stock-reason').value;
    
    let newStock = currentStock;
    
    if (action === 'add') {
        newStock = currentStock + quantity;
    } else if (action === 'set') {
        newStock = quantity;
    } else if (action === 'deduct') {
        newStock = currentStock - quantity;
        if (newStock < 0) {
            alert('Cannot deduct more than current stock.');
            return;
        }
    }
    
    const data = {
        product_id: productId,
        new_stock: newStock,
        reason: reason
    };
    
    fetch(`${API_BASE_URL}?action=update_stock&vendor_id=${VENDOR_ID}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            throw new Error(result.error);
        }
        
        alert(`Stock updated successfully! New stock: ${result.new_stock}`);
        closeModals();
        loadDashboardData();
    })
    .catch(error => {
        alert('Error updating stock: ' + error.message);
    });
}

// Export products
function exportProducts() {
    alert('Exporting products data...');
}

// Initialize charts
function initializeCharts() {
    const salesCtx = document.getElementById('sales-chart').getContext('2d');
    const productsCtx = document.getElementById('products-chart').getContext('2d');
    
    salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales ($)',
                data: [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                borderColor: '#4361ee',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    productsChart = new Chart(productsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Electronics', 'Clothing', 'Home', 'Sports'],
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: [
                    '#4361ee',
                    '#4cc9f0',
                    '#f72585',
                    '#4895ef'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Update charts with data
function updateCharts(stats) {
    const salesData = [450, 620, 580, 730, 810, 690, 550];
    salesChart.data.datasets[0].data = salesData;
    salesChart.update();
    
    const productsData = [12, 5, 4, 3];
    productsChart.data.datasets[0].data = productsData;
    productsChart.update();
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Add hidden input for product ID in stock form
if (!document.getElementById('stock-product-id')) {
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.id = 'stock-product-id';
    stockForm.appendChild(hiddenInput);
}

// Initialize the dashboard when loaded
document.addEventListener('DOMContentLoaded', initVendorDashboard);