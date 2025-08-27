document.addEventListener('DOMContentLoaded', () => {
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

    // --- Populate select options dynamically ---
    function populateSelect(action, selectElement, textField, valueField) {
        fetch(`../actions/product_action.php?action=${action}`)
            .then(res => res.json())
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
            });
    }

    populateSelect('categories', productCategorySelect, 'category_name', 'category_id');
    populateSelect('vendors', productVendorSelect, 'vendor_name', 'vendor_id');
    populateSelect('discounts', productDiscountSelect, 'discount_name', 'discount_id');

    // --- Open modal ---
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
            document.getElementById('productName').value = product.name;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productStock').value = product.stock;

            // Wait for select options to populate before setting value
            setTimeout(() => {
                productCategorySelect.value = product.category_id || '';
                productVendorSelect.value = product.vendor_id || '';
                productDiscountSelect.value = product.discount_id || '';
            }, 100);
        }
    }

    addProductBtn.addEventListener('click', () => openModal('add'));
    closeBtn.addEventListener('click', () => addProductModal.style.display = 'none');
    cancelBtn.addEventListener('click', () => addProductModal.style.display = 'none');

    window.addEventListener('click', e => {
        if (e.target === addProductModal) addProductModal.style.display = 'none';
    });

    // --- Load products dynamically ---
    function loadProducts() {
        fetch('../actions/product_action.php?action=list')
            .then(res => res.json())
            .then(products => {
                tbody.innerHTML = '';
                products.forEach(p => {
                    const tr = document.createElement('tr');
                    tr.dataset.id = p.product_id;
                    tr.innerHTML = `
                        <td>#P${p.product_id}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="${p.image_url || 'https://via.placeholder.com/50'}" class="product-image" alt="${p.name}">
                                <div>${p.name}</div>
                            </div>
                        </td>
                        <td>${p.category_name}</td>
                        <td>$${p.price}</td>
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
                attachEditDeleteEvents();
            });
    }

    // --- Add/Edit product form submit ---
    addProductForm.addEventListener('submit', e => {
        e.preventDefault();
        const id = productIdInput.value;
        const action = id ? 'edit' : 'add';
        const formData = new FormData(addProductForm);
        formData.append('id', id); // ensure ID is sent in POST

        fetch(`../actions/product_action.php?action=${action}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(id ? 'Product updated!' : 'Product added!');
                addProductModal.style.display = 'none';
                loadProducts();
            } else alert(data.message);
        });
    });

    // --- Edit/Delete buttons ---
    function attachEditDeleteEvents() {
    // Delete button
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.onclick = () => {
            const tr = btn.closest('tr');
            const id = tr.dataset.id;
            if (confirm('Are you sure you want to delete this product?')) {
                fetch(`../actions/product_action.php?action=delete&id=${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Product deleted!');
                            loadProducts();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        };
    });

    // Edit button
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = () => {
            const tr = btn.closest('tr');
            const id = tr.dataset.id;
            alert(id);

            // Fetch product data via GET
            fetch(`../actions/product_action.php?action=get&id=${id}`)
                .then(res => res.json())
                .then(product => {
                    if (!product.success) {
                        alert(product.message || 'An error occurred while fetching product data.');
                        return;
                    }

                    // Open modal and fill form fields
                    openModal('edit', product.data);

                    // Change submit button text
                    //document.getElementById('submitProductBtn').innerText = 'Update Product';
                });
        };
    });
}


    // --- Initial load ---
    loadProducts();
});
