// DOM Elements
const discountModal = document.getElementById('discount-modal');
const deleteModal = document.getElementById('delete-modal');
const closeModalBtns = document.querySelectorAll('.close-modal');
const cancelDiscountBtn = document.getElementById('cancel-discount');
const cancelDeleteBtn = document.getElementById('cancel-delete');
const discountForm = document.getElementById('discount-form');
const addDiscountBtn = document.getElementById('add-discount');
const confirmDeleteBtn = document.getElementById('confirm-delete');
const applyToSelect = document.getElementById('apply-to');
const productsSelectionGroup = document.getElementById('products-selection-group');
const categoriesSelectionGroup = document.getElementById('categories-selection-group');
const productsSelection = document.getElementById('products-selection');
const categoriesSelection = document.getElementById('categories-selection');
const tabs = document.querySelectorAll('.tab');
const discountsContainer = document.getElementById('discounts-container');
const exportDiscountsBtn = document.getElementById('export-discounts');

// API Configuration
const API_BASE_URL = '/api/vendor/discounts';

// Current state
let currentTab = 'active';
let currentDiscounts = [];
let currentProducts = [];
let currentCategories = [];
let discountToDelete = null;
let currentDiscountId = null; // For edit mode
let currentVendor = {};

// Initialize the discounts page
function initDiscountsPage() {
    loadVendorData();
    setupEventListeners();
    loadDiscountsData();
    loadProductsData();
    loadCategoriesData();
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
    
    // Tab navigation
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    // Buttons
    addDiscountBtn.addEventListener('click', () => openDiscountModal());
    exportDiscountsBtn.addEventListener('click', exportDiscounts);
    
    // Modals
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModals);
    });
    
    cancelDiscountBtn.addEventListener('click', closeModals);
    cancelDeleteBtn.addEventListener('click', closeModals);
    
    discountForm.addEventListener('submit', saveDiscount);
    confirmDeleteBtn.addEventListener('click', deleteDiscount);
    
    // Form interactions
    applyToSelect.addEventListener('change', handleApplyToChange);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === discountModal) closeModals();
        if (e.target === deleteModal) closeModals();
    });
}

// API Helper Function
async function apiRequest(action, method = 'GET', data = null) {
    try {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include' // Include session cookies
        };
        
        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }
        
        const url = `${API_BASE_URL}?action=${action}`;
        const response = await fetch(url, options);
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'API request failed');
        }
        
        return result.data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Load vendor data
async function loadVendorData() {
    try {
        const vendor = await apiRequest('vendor_info');
        currentVendor = vendor;
        document.getElementById('vendor-name').textContent = vendor.vendor_name;
        document.getElementById('vendor-email').textContent = vendor.contact_email || vendor.email;
    } catch (error) {
        console.error('Error loading vendor data:', error);
        alert('Error loading vendor information. Please refresh the page.');
    }
}

// Switch between tabs
function switchTab(tabName) {
    currentTab = tabName;
    
    // Update active tab
    tabs.forEach(tab => {
        if (tab.getAttribute('data-tab') === tabName) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Filter and render discounts
    const filteredDiscounts = filterDiscountsByTab(currentDiscounts, tabName);
    renderDiscounts(filteredDiscounts);
}

// Filter discounts by tab
function filterDiscountsByTab(discounts, tab) {
    const now = new Date();
    
    switch(tab) {
        case 'active':
            return discounts.filter(d => 
                d.is_active == 1 && 
                new Date(d.start_date) <= now && 
                new Date(d.end_date) >= now
            );
        case 'upcoming':
            return discounts.filter(d => 
                d.is_active == 1 && 
                new Date(d.start_date) > now
            );
        case 'inactive':
            return discounts.filter(d => d.is_active == 0);
        case 'expired':
            return discounts.filter(d => 
                new Date(d.end_date) < now
            );
        case 'all':
            return discounts;
        default:
            return discounts;
    }
}

// Load discounts data
async function loadDiscountsData() {
    try {
        const discounts = await apiRequest('discounts');
        currentDiscounts = discounts;
        updateDiscountCounts(discounts);
        
        // Filter discounts by current tab
        const filteredDiscounts = filterDiscountsByTab(discounts, currentTab);
        renderDiscounts(filteredDiscounts);
    } catch (error) {
        console.error('Error loading discounts:', error);
        alert('Error loading discounts. Please try again.');
    }
}

// Load products data
async function loadProductsData() {
    try {
        const products = await apiRequest('products');
        currentProducts = products;
        renderProductsSelection(products);
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Load categories data
async function loadCategoriesData() {
    try {
        const categories = await apiRequest('categories');
        currentCategories = categories;
        renderCategoriesSelection(categories);
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

// Update discount counts in stats and tabs
function updateDiscountCounts(discounts) {
    const now = new Date();
    
    // Calculate counts
    const activeCount = discounts.filter(d => 
        d.is_active == 1 && 
        new Date(d.start_date) <= now && 
        new Date(d.end_date) >= now
    ).length;
    
    const upcomingCount = discounts.filter(d => 
        d.is_active == 1 && 
        new Date(d.start_date) > now
    ).length;
    
    const inactiveCount = discounts.filter(d => d.is_active == 0).length;
    
    const expiredCount = discounts.filter(d => 
        new Date(d.end_date) < now
    ).length;
    
    // Update stats cards
    document.getElementById('active-count').textContent = activeCount;
    document.getElementById('upcoming-count').textContent = upcomingCount;
    document.getElementById('expired-count').textContent = expiredCount;
    
    // Update tab badges
    document.getElementById('active-tab-count').textContent = activeCount;
    document.getElementById('upcoming-tab-count').textContent = upcomingCount;
    document.getElementById('inactive-tab-count').textContent = inactiveCount;
    document.getElementById('expired-tab-count').textContent = expiredCount;
    document.getElementById('all-tab-count').textContent = discounts.length;
}

// Render discounts
function renderDiscounts(discounts) {
    discountsContainer.innerHTML = '';
    
    if (discounts.length === 0) {
        discountsContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-tags"></i>
                <h3>No ${currentTab} discounts</h3>
                <p>There are no ${currentTab} discounts matching your criteria.</p>
                ${currentTab === 'active' ? `
                    <button class="btn btn-primary" id="create-first-discount">
                        <i class="fas fa-plus"></i> Create Your First Discount
                    </button>
                ` : ''}
            </div>
        `;
        
        if (currentTab === 'active') {
            document.getElementById('create-first-discount').addEventListener('click', () => openDiscountModal());
        }
        return;
    }
    
    discounts.forEach(discount => {
        const discountCard = document.createElement('div');
        discountCard.className = 'discount-card';
        
        // Determine status
        const now = new Date();
        const startDate = new Date(discount.start_date);
        const endDate = new Date(discount.end_date);
        
        let status = 'inactive';
        let statusClass = 'status-inactive';
        
        if (discount.is_active == 1) {
            if (startDate > now) {
                status = 'upcoming';
                statusClass = 'status-upcoming';
            } else if (endDate < now) {
                status = 'expired';
                statusClass = 'status-expired';
            } else {
                status = 'active';
                statusClass = 'status-active';
            }
        }
        
        // Format discount value
        const discountValue = discount.discount_type === 'percentage' 
            ? `${discount.discount_value}%` 
            : `$${discount.discount_value}`;
        
        // Get scope description
        const scopeDescription = getScopeDescription(discount);
        
        discountCard.innerHTML = `
            <div class="discount-header">
                <div>
                    <div class="discount-name">${discount.discount_name}</div>
                    <div class="discount-type">${discount.discount_type === 'percentage' ? 'Percentage Discount' : 'Fixed Amount Discount'}</div>
                </div>
                <div class="discount-status ${statusClass}">${status}</div>
            </div>
            
            <div class="discount-details">
                <div class="discount-value">${discountValue} OFF</div>
                
                <div class="discount-dates">
                    <div class="date-item">
                        <div class="date-label">Starts</div>
                        <div class="date-value">${formatDate(discount.start_date)}</div>
                    </div>
                    <div class="date-item">
                        <div class="date-label">Ends</div>
                        <div class="date-value">${formatDate(discount.end_date)}</div>
                    </div>
                </div>
                
                <div class="discount-scope">
                    <div class="scope-label">Applied To:</div>
                    <div class="scope-value">${scopeDescription}</div>
                </div>
                
                <div class="products-count">
                    <i class="fas fa-box"></i> ${discount.products_count || 0} products affected
                </div>
                
                <div class="discount-actions">
                    <button class="btn btn-outline btn-sm view-discount-products" data-id="${discount.discount_id}">
                        <i class="fas fa-eye"></i> View Products
                    </button>
                    <button class="btn btn-primary btn-sm edit-discount" data-id="${discount.discount_id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm delete-discount" data-id="${discount.discount_id}" data-name="${discount.discount_name}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
        
        discountsContainer.appendChild(discountCard);
    });
    
    // Add event listeners to action buttons
    document.querySelectorAll('.edit-discount').forEach(button => {
        button.addEventListener('click', (e) => {
            const discountId = e.target.closest('button').getAttribute('data-id');
            openDiscountModal(discountId);
        });
    });
    
    document.querySelectorAll('.delete-discount').forEach(button => {
        button.addEventListener('click', (e) => {
            const discountId = e.target.closest('button').getAttribute('data-id');
            const discountName = e.target.closest('button').getAttribute('data-name');
            confirmDelete(discountId, discountName);
        });
    });
    
    document.querySelectorAll('.view-discount-products').forEach(button => {
        button.addEventListener('click', (e) => {
            const discountId = e.target.closest('button').getAttribute('data-id');
            viewDiscountProducts(discountId);
        });
    });
}

// Get scope description for display
function getScopeDescription(discount) {
    switch(discount.apply_to) {
        case 'all':
            return 'All Products';
        case 'selected':
            return `${discount.products_count || 0} Selected Products`;
        case 'categories':
            return 'Selected Categories';
        default:
            return 'All Products';
    }
}

// Render products selection
function renderProductsSelection(products) {
    productsSelection.innerHTML = '';
    
    products.forEach(product => {
        const productCheckbox = document.createElement('div');
        productCheckbox.className = 'product-checkbox';
        productCheckbox.innerHTML = `
            <input type="checkbox" id="product-${product.product_id}" value="${product.product_id}">
            <div class="product-info">
                <img src="${product.image_url || 'https://via.placeholder.com/100'}" alt="${product.name}" class="product-image">
                <div class="product-details">
                    <div class="product-name">${product.name}</div>
                    <div class="product-price">$${product.price}</div>
                </div>
            </div>
        `;
        productsSelection.appendChild(productCheckbox);
    });
}

// Render categories selection
function renderCategoriesSelection(categories) {
    categoriesSelection.innerHTML = '';
    
    categories.forEach(category => {
        const categoryCheckbox = document.createElement('div');
        categoryCheckbox.className = 'product-checkbox';
        categoryCheckbox.innerHTML = `
            <input type="checkbox" id="category-${category.category_id}" value="${category.category_id}">
            <div class="product-details">
                <div class="product-name">${category.category_name}</div>
            </div>
        `;
        categoriesSelection.appendChild(categoryCheckbox);
    });
}

// Open discount modal (for create/edit)
async function openDiscountModal(discountId = null) {
    const modalTitle = document.getElementById('discount-modal-title');
    currentDiscountId = discountId;
    
    if (discountId) {
        modalTitle.textContent = 'Edit Discount';
        
        try {
            const discount = await apiRequest(`discount&discount_id=${discountId}`);
            
            document.getElementById('discount-name').value = discount.discount_name;
            document.getElementById('discount-type').value = discount.discount_type;
            document.getElementById('discount-value').value = discount.discount_value;
            document.getElementById('start-date').value = formatDateTimeLocal(new Date(discount.start_date));
            document.getElementById('end-date').value = formatDateTimeLocal(new Date(discount.end_date));
            document.getElementById('apply-to').value = discount.apply_to;
            document.getElementById('discount-status').value = discount.is_active == 1 ? 'active' : 'inactive';
            
            // Handle apply to change to show proper sections
            handleApplyToChange();
            
            // Pre-select products or categories
            if (discount.apply_to === 'selected' && discount.selected_products) {
                discount.selected_products.forEach(productId => {
                    const checkbox = document.getElementById(`product-${productId}`);
                    if (checkbox) checkbox.checked = true;
                });
            } else if (discount.apply_to === 'categories' && discount.selected_categories) {
                discount.selected_categories.forEach(categoryId => {
                    const checkbox = document.getElementById(`category-${categoryId}`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        } catch (error) {
            alert('Error loading discount details: ' + error.message);
            return;
        }
    } else {
        modalTitle.textContent = 'Create New Discount';
        document.getElementById('discount-form').reset();
        
        // Set default dates
        const now = new Date();
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        document.getElementById('start-date').value = formatDateTimeLocal(now);
        document.getElementById('end-date').value = formatDateTimeLocal(tomorrow);
        
        // Handle apply to change
        handleApplyToChange();
    }
    
    discountModal.style.display = 'flex';
}

// Handle apply to selection change
function handleApplyToChange() {
    const applyTo = document.getElementById('apply-to').value;
    
    // Hide all selection groups
    productsSelectionGroup.style.display = 'none';
    categoriesSelectionGroup.style.display = 'none';
    
    // Show relevant selection group
    if (applyTo === 'selected') {
        productsSelectionGroup.style.display = 'block';
    } else if (applyTo === 'categories') {
        categoriesSelectionGroup.style.display = 'block';
    }
}

// Confirm discount deletion
function confirmDelete(discountId, discountName) {
    discountToDelete = discountId;
    document.getElementById('delete-discount-name').textContent = discountName;
    deleteModal.style.display = 'flex';
}

// Close all modals
function closeModals() {
    discountModal.style.display = 'none';
    deleteModal.style.display = 'none';
    discountToDelete = null;
    currentDiscountId = null;
}

// Save discount (create or edit)
async function saveDiscount(e) {
    e.preventDefault();
    
    const discountData = {
        discount_name: document.getElementById('discount-name').value,
        discount_type: document.getElementById('discount-type').value,
        discount_value: parseFloat(document.getElementById('discount-value').value),
        start_date: document.getElementById('start-date').value,
        end_date: document.getElementById('end-date').value,
        apply_to: document.getElementById('apply-to').value,
        is_active: document.getElementById('discount-status').value === 'active'
    };
    
    // Validate form
    if (!discountData.discount_name || !discountData.discount_type || 
        !discountData.discount_value || !discountData.start_date || 
        !discountData.end_date) {
        alert('Please fill in all required fields.');
        return;
    }
    
    // Validate dates
    const startDate = new Date(discountData.start_date);
    const endDate = new Date(discountData.end_date);
    
    if (endDate <= startDate) {
        alert('End date must be after start date.');
        return;
    }
    
    // Validate discount value
    if (discountData.discount_type === 'percentage' && 
        (discountData.discount_value <= 0 || discountData.discount_value > 100)) {
        alert('Percentage discount must be between 0 and 100.');
        return;
    }
    
    if (discountData.discount_type === 'fixed' && discountData.discount_value <= 0) {
        alert('Fixed discount must be greater than 0.');
        return;
    }
    
    // Get selected products or categories
    if (discountData.apply_to === 'selected') {
        const selectedProducts = Array.from(productsSelection.querySelectorAll('input:checked'))
            .map(input => parseInt(input.value));
        discountData.selected_products = selectedProducts;
        
        if (selectedProducts.length === 0) {
            alert('Please select at least one product.');
            return;
        }
    } else if (discountData.apply_to === 'categories') {
        const selectedCategories = Array.from(categoriesSelection.querySelectorAll('input:checked'))
            .map(input => parseInt(input.value));
        discountData.selected_categories = selectedCategories;
        
        if (selectedCategories.length === 0) {
            alert('Please select at least one category.');
            return;
        }
    }
    
    try {
        if (currentDiscountId) {
            // Update existing discount
            discountData.discount_id = currentDiscountId;
            await apiRequest('update_discount', 'PUT', discountData);
            alert('Discount updated successfully!');
        } else {
            // Create new discount
            await apiRequest('create_discount', 'POST', discountData);
            alert('Discount created successfully!');
        }
        
        closeModals();
        loadDiscountsData(); // Refresh discounts
    } catch (error) {
        alert('Error saving discount: ' + error.message);
    }
}

// Delete discount
async function deleteDiscount() {
    if (!discountToDelete) return;
    
    try {
        await apiRequest(`delete_discount&discount_id=${discountToDelete}`, 'DELETE');
        alert('Discount deleted successfully!');
        closeModals();
        loadDiscountsData(); // Refresh discounts
        discountToDelete = null;
    } catch (error) {
        alert('Error deleting discount: ' + error.message);
    }
}

// View discount products
async function viewDiscountProducts(discountId) {
    try {
        const products = await apiRequest(`discount_products&discount_id=${discountId}`);
        
        let productsList = '<ul>';
        products.forEach(product => {
            productsList += `<li>${product.name} - $${product.price}</li>`;
        });
        productsList += '</ul>';
        
        alert(`Products affected by this discount:\n\n${products.map(p => `${p.name} - $${p.price}`).join('\n')}`);
    } catch (error) {
        alert('Error loading discount products: ' + error.message);
    }
}

// Export discounts
function exportDiscounts() {
    // Convert discounts to CSV
    const csvContent = convertToCSV(currentDiscounts);
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `discounts_export_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Convert discounts to CSV
function convertToCSV(discounts) {
    const headers = ['Discount Name', 'Type', 'Value', 'Start Date', 'End Date', 'Apply To', 'Status', 'Products Count'];
    const rows = discounts.map(d => [
        d.discount_name,
        d.discount_type,
        d.discount_value,
        d.start_date,
        d.end_date,
        d.apply_to,
        d.is_active == 1 ? 'Active' : 'Inactive',
        d.products_count || 0
    ]);
    
    let csv = headers.join(',') + '\n';
    rows.forEach(row => {
        csv += row.map(cell => `"${cell}"`).join(',') + '\n';
    });
    
    return csv;
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Format date for datetime-local input
function formatDateTimeLocal(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Initialize the page when loaded
document.addEventListener('DOMContentLoaded', initDiscountsPage);