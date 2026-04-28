// discount_manage.js - Simplified for Automatic Discounts
const API_BASE_URL = '/api/admin/discounts';
const DISCOUNTS_ENDPOINT = `${API_BASE_URL}/discounts`;
const PRODUCTS_ENDPOINT = `${API_BASE_URL}/products`;
const CATEGORIES_ENDPOINT = `${API_BASE_URL}/categories`;

// State management
let currentPage = 1;
let totalPages = 1;
let currentFilters = {
    search: '',
    status: 'all',
    type: 'all'
};
let discountToDelete = null;
let currentDiscountId = null;
let selectedProducts = [];
let selectedCategories = [];

// DOM Elements
const discountsTableBody = document.getElementById('discountsTableBody');
const discountCountSpan = document.getElementById('discountCount');
const paginationDiv = document.getElementById('pagination');
const discountModal = document.getElementById('discountModal');
const viewProductsModal = document.getElementById('viewProductsModal');
const confirmationModal = document.getElementById('confirmationModal');
const discountForm = document.getElementById('discountForm');
const modalTitle = document.getElementById('modalTitle');
const submitBtn = document.getElementById('submitBtn');
const searchInput = document.getElementById('search');
const statusFilter = document.getElementById('statusFilter');
const typeFilter = document.getElementById('typeFilter');
const applyFiltersBtn = document.getElementById('applyFilters');

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadDiscounts();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    document.getElementById('addDiscountBtn').addEventListener('click', () => openDiscountModal());
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            closeDiscountModal();
            closeViewProductsModal();
        });
    });
    document.getElementById('cancelBtn').addEventListener('click', () => closeDiscountModal());
    document.getElementById('closeViewProductsBtn').addEventListener('click', () => closeViewProductsModal());
    
    discountForm.addEventListener('submit', handleDiscountFormSubmit);
    document.getElementById('discountType').addEventListener('change', handleDiscountTypeChange);
    document.getElementById('applyTo').addEventListener('change', handleApplyToChange);
    document.getElementById('productSearch').addEventListener('input', handleProductSearch);
    
    applyFiltersBtn.addEventListener('click', applyFilters);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') applyFilters();
    });
    
    document.getElementById('confirmCancel').addEventListener('click', () => closeConfirmationModal());
    document.getElementById('confirmAction').addEventListener('click', handleConfirmedAction);
    
    window.addEventListener('click', (e) => {
        if (e.target === discountModal) closeDiscountModal();
        if (e.target === viewProductsModal) closeViewProductsModal();
        if (e.target === confirmationModal) closeConfirmationModal();
    });
}

// Load discounts
function loadDiscounts(page = 1) {
    showLoadingState(true);
    
    const params = new URLSearchParams({
        page: page,
        limit: 10,
        search: currentFilters.search,
        status: currentFilters.status,
        type: currentFilters.type
    });
    
    fetch(`${DISCOUNTS_ENDPOINT}?${params}`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch discounts');
            return response.json();
        })
        .then(data => {
            displayDiscounts(data.discounts);
            updatePagination(data.pagination);
            currentPage = data.pagination.current_page;
            totalPages = data.pagination.total_pages;
            updateDiscountCount(data.pagination.total);
        })
        .catch(error => {
            console.error('Error loading discounts:', error);
            showNotification('Failed to load discounts', 'error');
            discountsTableBody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: red;">Error loading discounts</td></tr>';
        })
        .finally(() => {
            showLoadingState(false);
        });
}

// Display discounts
function displayDiscounts(discounts) {
    if (discounts.length === 0) {
        discountsTableBody.innerHTML = '<tr><td colspan="8" style="text-align: center;">No discounts found</td></tr>';
        return;
    }
    
    discountsTableBody.innerHTML = discounts.map(discount => `
        <tr>
            <td>#${discount.id}</td>
            <td>${escapeHtml(discount.name)}</td>
            <td><span class="discount-type type-${discount.type}">${discount.type.charAt(0).toUpperCase() + discount.type.slice(1)}</span></td>
            <td>${discount.type === 'percentage' ? `${discount.value}%` : `$${discount.value.toFixed(2)}`}</td>
            <td>${formatDate(discount.start_date)}</td>
            <td>${formatDate(discount.end_date)}</td>
            <td>${discount.products_count} ${discount.products_count === 1 ? 'item' : 'items'}</td>
            <td><span class="discount-status status-${getDiscountStatus(discount)}">${getDiscountStatus(discount).charAt(0).toUpperCase() + getDiscountStatus(discount).slice(1)}</span></td>
            <td class="action-buttons">
                <button class="btn btn-success btn-sm view-products-btn" data-id="${discount.id}">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn btn-primary btn-sm edit-discount-btn" data-id="${discount.id}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm delete-discount-btn" data-id="${discount.id}">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
    
    document.querySelectorAll('.view-products-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const discountId = e.target.closest('.view-products-btn').dataset.id;
            viewDiscountProducts(discountId);
        });
    });
    
    document.querySelectorAll('.edit-discount-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const discountId = e.target.closest('.edit-discount-btn').dataset.id;
            editDiscount(discountId);
        });
    });
    
    document.querySelectorAll('.delete-discount-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const discountId = e.target.closest('.delete-discount-btn').dataset.id;
            confirmDeleteDiscount(discountId);
        });
    });
}

// Update pagination
function updatePagination(pagination) {
    const { current_page, total_pages } = pagination;
    
    if (total_pages <= 1) {
        paginationDiv.innerHTML = '';
        return;
    }
    
    let paginationHTML = '';
    
    if (current_page > 1) {
        paginationHTML += `<button onclick="loadDiscounts(${current_page - 1})">&laquo;</button>`;
    } else {
        paginationHTML += `<button disabled>&laquo;</button>`;
    }
    
    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(total_pages, current_page + 2);
    
    if (startPage > 1) {
        paginationHTML += `<button onclick="loadDiscounts(1)">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<button disabled>...</button>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === current_page) {
            paginationHTML += `<button class="active">${i}</button>`;
        } else {
            paginationHTML += `<button onclick="loadDiscounts(${i})">${i}</button>`;
        }
    }
    
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            paginationHTML += `<button disabled>...</button>`;
        }
        paginationHTML += `<button onclick="loadDiscounts(${total_pages})">${total_pages}</button>`;
    }
    
    if (current_page < total_pages) {
        paginationHTML += `<button onclick="loadDiscounts(${current_page + 1})">&raquo;</button>`;
    } else {
        paginationHTML += `<button disabled>&raquo;</button>`;
    }
    
    paginationDiv.innerHTML = paginationHTML;
}

// Update discount count
function updateDiscountCount(count) {
    discountCountSpan.textContent = `Total discounts: ${count}`;
}

// Open discount modal
function openDiscountModal(discount = null) {
    if (discount) {
        modalTitle.textContent = 'Edit Discount';
        submitBtn.textContent = 'Update Discount';
        currentDiscountId = discount.id;
        document.getElementById('discountId').value = discount.id;
        document.getElementById('discountName').value = discount.name;
        document.getElementById('discountType').value = discount.type;
        document.getElementById('discountValue').value = discount.value;
        document.getElementById('startDate').value = formatDateTimeForInput(discount.start_date);
        document.getElementById('endDate').value = formatDateTimeForInput(discount.end_date);
        document.getElementById('applyTo').value = discount.apply_to;
        
        selectedProducts = discount.products || [];
        selectedCategories = discount.categories || [];
        
        handleApplyToChange();
    } else {
        modalTitle.textContent = 'Add New Discount';
        submitBtn.textContent = 'Add Discount';
        currentDiscountId = null;
        discountForm.reset();
        document.getElementById('discountId').value = '';
        selectedProducts = [];
        selectedCategories = [];
    }
    
    handleDiscountTypeChange();
    discountModal.style.display = 'flex';
}

// Close discount modal
function closeDiscountModal() {
    discountModal.style.display = 'none';
    discountForm.reset();
    selectedProducts = [];
    selectedCategories = [];
    currentDiscountId = null;
}

// Handle discount form submission
function handleDiscountFormSubmit(e) {
    e.preventDefault();
    
    const discountId = document.getElementById('discountId').value;
    const discountData = {
        name: document.getElementById('discountName').value.trim(),
        type: document.getElementById('discountType').value,
        value: parseFloat(document.getElementById('discountValue').value),
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value,
        apply_to: document.getElementById('applyTo').value
    };
    
    if (!discountData.name) {
        showNotification('Please enter discount name', 'error');
        return;
    }
    
    if (discountData.value <= 0) {
        showNotification('Discount value must be greater than 0', 'error');
        return;
    }
    
    if (discountData.type === 'percentage' && discountData.value > 100) {
        showNotification('Percentage cannot exceed 100%', 'error');
        return;
    }
    
    if (discountData.apply_to === 'selected') {
        if (selectedProducts.length === 0) {
            showNotification('Please select at least one product', 'error');
            return;
        }
        discountData.products = selectedProducts;
    } else if (discountData.apply_to === 'categories') {
        if (selectedCategories.length === 0) {
            showNotification('Please select at least one category', 'error');
            return;
        }
        discountData.categories = selectedCategories;
    }
    
    if (discountId) {
        updateDiscount(discountId, discountData);
    } else {
        addDiscount(discountData);
    }
}

// Add new discount
function addDiscount(discountData) {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';
    
    fetch(DISCOUNTS_ENDPOINT, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(discountData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { 
                throw new Error(err.message || 'Failed to add discount'); 
            });
        }
        return response.json();
    })
    .then(data => {
        showNotification('Discount added successfully', 'success');
        closeDiscountModal();
        loadDiscounts(currentPage);
    })
    .catch(error => {
        console.error('Error adding discount:', error);
        showNotification(error.message || 'Failed to add discount', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add Discount';
    });
}

// Edit discount
function editDiscount(discountId) {
    showLoadingState(true);
    
    fetch(`${DISCOUNTS_ENDPOINT}/${discountId}`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch discount data');
            return response.json();
        })
        .then(discount => {
            openDiscountModal(discount);
        })
        .catch(error => {
            console.error('Error fetching discount:', error);
            showNotification('Failed to fetch discount data', 'error');
        })
        .finally(() => {
            showLoadingState(false);
        });
}

// Update discount
function updateDiscount(discountId, discountData) {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    fetch(`${DISCOUNTS_ENDPOINT}/${discountId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(discountData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { 
                throw new Error(err.message || 'Failed to update discount'); 
            });
        }
        return response.json();
    })
    .then(data => {
        showNotification('Discount updated successfully', 'success');
        closeDiscountModal();
        loadDiscounts(currentPage);
    })
    .catch(error => {
        console.error('Error updating discount:', error);
        showNotification(error.message || 'Failed to update discount', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update Discount';
    });
}

// View discount products
function viewDiscountProducts(discountId) {
    fetch(`${DISCOUNTS_ENDPOINT}/${discountId}/products`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch discount products');
            return response.json();
        })
        .then(data => {
            displayDiscountProducts(discountId, data.products);
        })
        .catch(error => {
            console.error('Error fetching discount products:', error);
            showNotification('Failed to fetch discount products', 'error');
        });
}

// Display discount products
function displayDiscountProducts(discountId, products) {
    document.getElementById('viewProductsTitle').textContent = `Products with Discount #${discountId}`;
    
    const productsList = document.getElementById('viewProductsList');
    if (products.length === 0) {
        productsList.innerHTML = '<p style="text-align: center; padding: 20px;">No products found for this discount</p>';
    } else {
        productsList.innerHTML = products.map(product => `
            <div class="product-item">
                <img src="${product.image_url || 'https://via.placeholder.com/50'}" alt="${escapeHtml(product.name)}" onerror="this.src='https://via.placeholder.com/50'">
                <div class="product-info">
                    <div class="product-name">${escapeHtml(product.name)}</div>
                    <div class="product-category">${escapeHtml(product.category)}</div>
                    ${product.discount_source ? `<small style="color: #666;">(via ${product.discount_source})</small>` : ''}
                </div>
                <div class="product-price">$${product.price.toFixed(2)}</div>
            </div>
        `).join('');
    }
    
    viewProductsModal.style.display = 'flex';
}

// Close view products modal
function closeViewProductsModal() {
    viewProductsModal.style.display = 'none';
}

// Confirm discount deletion
function confirmDeleteDiscount(discountId) {
    discountToDelete = discountId;
    document.getElementById('confirmationTitle').textContent = 'Confirm Deletion';
    document.getElementById('confirmationMessage').textContent = 'Are you sure you want to delete this discount? This action cannot be undone.';
    confirmationModal.style.display = 'flex';
}

// Handle confirmed actions
function handleConfirmedAction() {
    if (discountToDelete) {
        deleteDiscount(discountToDelete);
    }
    closeConfirmationModal();
}

// Close confirmation modal
function closeConfirmationModal() {
    confirmationModal.style.display = 'none';
    discountToDelete = null;
}

// Delete discount
function deleteDiscount(discountId) {
    fetch(`${DISCOUNTS_ENDPOINT}/${discountId}`, {
        method: 'DELETE'
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { 
                throw new Error(err.message || 'Failed to delete discount'); 
            });
        }
        return response.json();
    })
    .then(data => {
        showNotification('Discount deleted successfully', 'success');
        loadDiscounts(currentPage);
    })
    .catch(error => {
        console.error('Error deleting discount:', error);
        showNotification(error.message || 'Failed to delete discount', 'error');
    });
}

// Handle discount type change
function handleDiscountTypeChange() {
    const discountType = document.getElementById('discountType').value;
    const valueLabel = document.getElementById('valueLabel');
    const valueInput = document.getElementById('discountValue');
    
    if (discountType === 'percentage') {
        valueLabel.textContent = 'Percentage (%)';
        valueInput.max = 100;
        valueInput.min = 0;
        valueInput.step = 0.01;
        valueInput.placeholder = 'Enter percentage (0-100)';
    } else if (discountType === 'fixed') {
        valueLabel.textContent = 'Amount ($)';
        valueInput.removeAttribute('max');
        valueInput.min = 0;
        valueInput.step = 0.01;
        valueInput.placeholder = 'Enter amount';
    }
}

// Handle apply to change
function handleApplyToChange() {
    const applyTo = document.getElementById('applyTo').value;
    const productSelector = document.getElementById('productSelector');
    const categorySelector = document.getElementById('categorySelector');
    
    productSelector.style.display = 'none';
    categorySelector.style.display = 'none';
    
    if (applyTo === 'selected') {
        productSelector.style.display = 'block';
        loadProducts();
    } else if (applyTo === 'categories') {
        categorySelector.style.display = 'block';
        loadCategories();
    }
}

// Load products
function loadProducts(search = '') {
    const params = new URLSearchParams();
    if (search) {
        params.append('search', search);
    }
    params.append('limit', 200);
    
    fetch(`${PRODUCTS_ENDPOINT}?${params}`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch products');
            return response.json();
        })
        .then(data => {
            displayProducts(data.products);
        })
        .catch(error => {
            console.error('Error loading products:', error);
            showNotification('Failed to load products', 'error');
        });
}

// Display products
function displayProducts(products) {
    const productsList = document.getElementById('productsList');
    
    if (products.length === 0) {
        productsList.innerHTML = '<p style="padding: 10px; text-align: center;">No products found</p>';
        return;
    }
    
    productsList.innerHTML = products.map(product => `
        <div class="product-checkbox">
            <input type="checkbox" id="product_${product.id}" value="${product.id}" 
                ${selectedProducts.includes(product.id) ? 'checked' : ''}>
            <label for="product_${product.id}">${escapeHtml(product.name)} - $${product.price.toFixed(2)}</label>
        </div>
    `).join('');
    
    document.querySelectorAll('#productsList input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            const productId = parseInt(e.target.value);
            if (e.target.checked) {
                if (!selectedProducts.includes(productId)) {
                    selectedProducts.push(productId);
                }
            } else {
                selectedProducts = selectedProducts.filter(id => id !== productId);
            }
        });
    });
}

// Load categories
function loadCategories() {
    fetch(CATEGORIES_ENDPOINT)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch categories');
            return response.json();
        })
        .then(data => {
            displayCategories(data.categories);
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            showNotification('Failed to load categories', 'error');
        });
}

// Display categories
function displayCategories(categories) {
    const categoriesList = document.getElementById('categoriesList');
    
    if (categories.length === 0) {
        categoriesList.innerHTML = '<p style="padding: 10px; text-align: center;">No categories found</p>';
        return;
    }
    
    categoriesList.innerHTML = categories.map(category => `
        <div class="product-checkbox">
            <input type="checkbox" id="category_${category.id}" value="${category.id}" 
                ${selectedCategories.includes(category.id) ? 'checked' : ''}>
            <label for="category_${category.id}">${escapeHtml(category.name)} (${category.products_count} products)</label>
        </div>
    `).join('');
    
    document.querySelectorAll('#categoriesList input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            const categoryId = parseInt(e.target.value);
            if (e.target.checked) {
                if (!selectedCategories.includes(categoryId)) {
                    selectedCategories.push(categoryId);
                }
            } else {
                selectedCategories = selectedCategories.filter(id => id !== categoryId);
            }
        });
    });
}

// Handle product search
function handleProductSearch(e) {
    const searchTerm = e.target.value;
    clearTimeout(window.productSearchTimeout);
    window.productSearchTimeout = setTimeout(() => {
        loadProducts(searchTerm);
    }, 300);
}

// Apply filters
function applyFilters() {
    currentFilters = {
        search: searchInput.value.trim(),
        status: statusFilter.value,
        type: typeFilter.value
    };
    loadDiscounts(1);
}

// Show loading state
function showLoadingState(loading) {
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        if (loading) {
            tableContainer.classList.add('loading');
        } else {
            tableContainer.classList.remove('loading');
        }
    }
}

// Show notification
function showNotification(message, type) {
    document.querySelectorAll('.notification').forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('show'), 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Utility functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function formatDateTimeForInput(dateString) {
    const date = new Date(dateString);
    return date.toISOString().slice(0, 16);
}

function getDiscountStatus(discount) {
    const now = new Date();
    const startDate = new Date(discount.start_date);
    const endDate = new Date(discount.end_date);
    
    if (!discount.is_active) return 'expired';
    if (now < startDate) return 'upcoming';
    if (now > endDate) return 'expired';
    return 'active';
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}