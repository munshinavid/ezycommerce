document.addEventListener('DOMContentLoaded', () => {
    // ===== API Configuration =====
    const API_URL = '../controllers/ProductAPI.php';
    
    // ===== DOM Elements =====
    const addProductBtn = document.getElementById('addProductBtn');
    const addProductModal = document.getElementById('addProductModal');
    const closeBtn = addProductModal.querySelector('.close-btn');
    const cancelBtn = document.getElementById('cancelProductBtn');
    const addProductForm = document.getElementById('addProductForm');
    const modalTitle = addProductModal.querySelector('h3');
    const submitBtn = document.getElementById('submitProductBtn');

    const productIdInput = document.getElementById('productId');
    const productCategorySelect = document.getElementById('productCategory');
    const productVendorSelect = document.getElementById('productVendor');
    const productDiscountSelect = document.getElementById('productDiscount');
    const tbody = document.querySelector('tbody');

    let selectPromises = [];

    // ===== Helper Functions =====
    
    // API call helper
    function apiCall(action, method = 'GET', data = null) {
        const url = `${API_URL}?action=${action}`;
        const options = { method };
        
        if (method === 'POST' && data) {
            options.body = data;
        }
        
        return fetch(url, options)
            .then(res => res.json())
            .catch(err => {
                console.error(`API Error (${action}):`, err);
                throw err;
            });
    }

    // Populate select options dynamically
    function populateSelect(action, selectElement, textField, valueField) {
        return apiCall(action)
            .then(data => {
                if (Array.isArray(data)) {
                    selectElement.innerHTML = '<option value="">Select</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item[valueField];
                        option.textContent = item[textField];
                        selectElement.appendChild(option);
                    });
                }
            })
            .catch(err => {
                console.error(`Error loading ${action}:`, err);
            });
    }

    // Initialize select options on page load
    selectPromises = [
        populateSelect('categories', productCategorySelect, 'category_name', 'category_id'),
        populateSelect('vendors', productVendorSelect, 'vendor_name', 'vendor_id'),
        populateSelect('discounts', productDiscountSelect, 'discount_name', 'discount_id')
    ];

    // ===== Modal Functions =====
    
    // Open modal for add/edit product
    function openModal(mode, product = {}) {
        addProductModal.style.display = 'flex';

        if (mode === 'add') {
            modalTitle.innerText = 'Add New Product';
            submitBtn.innerText = 'Add Product';
            addProductForm.reset();
            productIdInput.value = '';
        } else if (mode === 'edit') {
            modalTitle.innerText = 'Edit Product';
            submitBtn.innerText = 'Update Product';
            productIdInput.value = product.product_id;
            document.getElementById('productName').value = product.name || '';
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productPrice').value = product.price || '';
            document.getElementById('productStock').value = product.stock || '';

            // Wait until selects are ready, then set values
            Promise.all(selectPromises).then(() => {
                productCategorySelect.value = product.category_id || '';
                productVendorSelect.value = product.vendor_id || '';
                productDiscountSelect.value = product.discount_id || '';
            });
        }
    }

    // Close modal
    function closeModal() {
        addProductModal.style.display = 'none';
    }

    // ===== Modal Event Listeners =====
    addProductBtn.addEventListener('click', () => openModal('add'));
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    window.addEventListener('click', e => {
        if (e.target === addProductModal) closeModal();
    });

    // ===== Product CRUD Operations =====
    
    // Load products dynamically
    function loadProducts() {
        apiCall('list')
            .then(products => {
                tbody.innerHTML = '';
                if (Array.isArray(products) && products.length > 0) {
                    products.forEach(p => {
                        const tr = document.createElement('tr');
                        tr.dataset.id = p.product_id;
                        tr.innerHTML = `
                            <td>#P${p.product_id}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <img src="${p.image_url || 'https://via.placeholder.com/50'}" class="product-image" alt="${p.name}" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                    <div>${p.name}</div>
                                </div>
                            </td>
                            <td>${p.category_name || '-'}</td>
                            <td>$${parseFloat(p.price).toFixed(2)}</td>
                            <td>${p.stock}</td>
                            <td>${p.vendor_name || '-'}</td>
                            <td><span class="stock-status ${p.stock > 0 ? 'in-stock' : 'out-of-stock'}">${p.stock > 0 ? 'In Stock' : 'Out of Stock'}</span></td>
                            <td class="action-buttons">
                                <button class="btn btn-success btn-sm edit-btn"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i> Delete</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No products found</td></tr>';
                }
                attachEditDeleteEvents();
            })
            .catch(err => {
                console.error('Error loading products:', err);
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:red;">Error loading products</td></tr>';
            });
    }

    // Add/Edit product form submit
    addProductForm.addEventListener('submit', e => {
        e.preventDefault();
        const id = productIdInput.value;
        const action = id ? 'edit' : 'add';
        const formData = new FormData(addProductForm);
        
        // Add product ID for edit action
        if (id) {
            formData.append('id', id);
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerText = id ? 'Updating...' : 'Adding...';

        apiCall(action, 'POST', formData)
            .then(data => {
                if (data.success) {
                    alert(data.message || (id ? 'Product updated successfully!' : 'Product added successfully!'));
                    closeModal();
                    loadProducts();
                } else {
                    alert(data.message || 'An error occurred');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to save product. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerText = id ? 'Update Product' : 'Add Product';
            });
    });

    // Edit/Delete button handlers
    function attachEditDeleteEvents() {
        // Delete button
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.onclick = () => {
                const tr = btn.closest('tr');
                const id = tr.dataset.id;
                if (confirm('Are you sure you want to delete this product?')) {
                    apiCall(`delete&id=${id}`)
                        .then(data => {
                            if (data.success) {
                                alert(data.message || 'Product deleted successfully!');
                                loadProducts();
                            } else {
                                alert(data.message || 'Failed to delete product');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Failed to delete product. Please try again.');
                        });
                }
            };
        });

        // Edit button
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.onclick = () => {
                const tr = btn.closest('tr');
                const id = tr.dataset.id;

                apiCall(`get&id=${id}`)
                    .then(response => {
                        if (response.success && response.data) {
                            openModal('edit', response.data);
                        } else {
                            alert(response.message || 'Failed to fetch product data');
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        alert('Failed to load product data. Please try again.');
                    });
            };
        });
    }

    // ===== Initialize =====
    loadProducts();
});