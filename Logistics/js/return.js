// Configuration
const API_BASE_URL = '../controllers/ReturnAPI.php';

// DOM Elements
const tabs = document.querySelectorAll('.tab');
const sidebarMenuItems = document.querySelectorAll('.sidebar-menu a[data-status]');
const filtersContent = document.getElementById('filters-content');
const toggleFiltersBtn = document.getElementById('toggle-filters');
const applyFiltersBtn = document.getElementById('apply-filters');
const resetFiltersBtn = document.getElementById('reset-filters');
const bulkActions = document.getElementById('bulk-actions');
const selectAllCheckbox = document.getElementById('select-all-returns');
const returnsBody = document.getElementById('returns-body');
const tableTitle = document.getElementById('table-title');
const returnModal = document.getElementById('return-modal');
const processModal = document.getElementById('process-modal');
const closeModalBtns = document.querySelectorAll('.close-modal');
const closeReturnModalBtn = document.getElementById('close-return-modal');
const cancelProcessBtn = document.getElementById('cancel-process');
const processForm = document.getElementById('process-form');
const processReturnStatus = document.getElementById('process-return-status');
const refundAmountGroup = document.getElementById('refund-amount-group');
const rejectionReasonGroup = document.getElementById('rejection-reason-group');
const refreshReturnsBtn = document.getElementById('refresh-returns');
const exportReturnsBtn = document.getElementById('export-returns');
const processReturnBtn = document.getElementById('process-return');

// Current filter state
let currentStatusFilter = 'all';
let currentReturns = [];
let allReturns = []; // Store all returns for filtering

// Initialize the page
function initReturnsPage() {
    loadReturnsCounts();
    loadReturns('all');
    setupEventListeners();
}

// Setup event listeners
function setupEventListeners() {
    // Tab navigation
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    // Sidebar menu navigation
    sidebarMenuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const status = item.getAttribute('data-status');
            
            // Handle refresh-all action
            if (status === 'refresh-all') {
                // Update active state
                sidebarMenuItems.forEach(i => i.parentElement.classList.remove('active'));
                item.parentElement.classList.add('active');
                
                // Set to 'all' status and refresh
                currentStatusFilter = 'all';
                clearReturnSelection();
                
                // Refresh counts and load all returns
                loadReturnsCounts();
                loadReturns('all');
                
                // Update tabs to show 'all'
                tabs.forEach(tab => {
                    if (tab.getAttribute('data-tab') === 'all') {
                        tab.classList.add('active');
                    } else {
                        tab.classList.remove('active');
                    }
                });
                
                return;
            }
            
            // Update active state
            sidebarMenuItems.forEach(i => i.parentElement.classList.remove('active'));
            item.parentElement.classList.add('active');
            
            // Update current filter and reload returns from server
            currentStatusFilter = status;
            
            // Clear selection when switching filters
            clearReturnSelection();
            
            // Fetch fresh data from server
            loadReturns(status);
            
            // Update tabs to match
            tabs.forEach(tab => {
                if (tab.getAttribute('data-tab') === status) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });
        });
    });
    
    // Filters
    toggleFiltersBtn.addEventListener('click', toggleFilters);
    applyFiltersBtn.addEventListener('click', applyFilters);
    resetFiltersBtn.addEventListener('click', resetFilters);
    
    // Bulk actions
    selectAllCheckbox.addEventListener('change', handleSelectAll);
    document.getElementById('apply-bulk-action').addEventListener('click', applyBulkAction);
    
    // Modals
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModals);
    });
    
    closeReturnModalBtn.addEventListener('click', closeModals);
    cancelProcessBtn.addEventListener('click', closeModals);
    processReturnBtn.addEventListener('click', openProcessModalFromDetails);
    
    processForm.addEventListener('submit', processReturn);
    
    processReturnStatus.addEventListener('change', function() {
        refundAmountGroup.style.display = ['Approved'].includes(this.value) ? 'block' : 'none';
        rejectionReasonGroup.style.display = this.value === 'Rejected' ? 'block' : 'none';
    });
    
    // Refresh and export buttons
    refreshReturnsBtn.addEventListener('click', refreshReturns);
    exportReturnsBtn.addEventListener('click', exportReturns);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === returnModal) closeModals();
        if (e.target === processModal) closeModals();
    });
}

// ============================================
// API FUNCTIONS
// ============================================

// Load returns counts
async function loadReturnsCounts() {
    try {
        const response = await fetch(`${API_BASE_URL}?action=counts`);
        const result = await response.json();
        
        if (result.success) {
            const counts = result.data;
            
            // Update sidebar counts
            document.getElementById('sidebar-pending-count').textContent = counts.pending;
            document.getElementById('sidebar-processing-count').textContent = counts.processing;
            document.getElementById('sidebar-approved-count').textContent = counts.approved;
            document.getElementById('sidebar-rejected-count').textContent = counts.rejected;
            
            // Update menu and tab counts
            document.getElementById('menu-all-count').textContent = counts.all;
            document.getElementById('menu-pending-count').textContent = counts.pending;
            document.getElementById('menu-processing-count').textContent = counts.processing;
            document.getElementById('menu-approved-count').textContent = counts.approved;
            document.getElementById('menu-rejected-count').textContent = counts.rejected;
            
            document.getElementById('all-count').textContent = counts.all;
            document.getElementById('pending-count').textContent = counts.pending;
            document.getElementById('processing-count').textContent = counts.processing;
            document.getElementById('approved-count').textContent = counts.approved;
            document.getElementById('rejected-count').textContent = counts.rejected;
        }
    } catch (error) {
        console.error('Error loading counts:', error);
        showNotification('Failed to load return counts', 'error');
    }
}

// Load returns by status
async function loadReturns(status) {
    try {
        // Show loading state
        returnsBody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Loading returns...</td></tr>';
        
        const response = await fetch(`${API_BASE_URL}?action=list&status=${status}`);
        const result = await response.json();
        
        if (result.success) {
            allReturns = result.data;
            currentReturns = result.data;
            renderReturnsTable(status, currentReturns);
            
            // Show success notification for filter changes (except initial load)
            if (document.readyState === 'complete') {
                const statusNames = {
                    'all': 'All Returns',
                    'pending': 'Pending Returns',
                    'processing': 'Processing Returns',
                    'approved': 'Approved Returns',
                    'rejected': 'Rejected Returns'
                };
                showNotification(`Loaded ${result.total} ${statusNames[status]}`, 'success');
            }
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Error loading returns:', error);
        returnsBody.innerHTML = '<tr><td colspan="9" style="text-align: center; color: red; padding: 40px;"><i class="fas fa-exclamation-triangle"></i> Failed to load returns</td></tr>';
        showNotification('Failed to load returns', 'error');
    }
}

// Get return details
async function loadReturnDetails(returnId) {
    try {
        const response = await fetch(`${API_BASE_URL}?action=details&id=${returnId}`);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Error loading return details:', error);
        showNotification('Failed to load return details', 'error');
        return null;
    }
}

// Process return
async function processReturnAPI(returnId, status, refundAmount = null, rejectionReason = null, internalNotes = null, customerMessage = null) {
    try {
        const payload = {
            return_id: returnId,
            status: status
        };
        
        if (refundAmount) {
            payload.refund_amount = refundAmount;
        }
        
        if (rejectionReason) {
            payload.rejection_reason = rejectionReason;
        }
        
        if (internalNotes) {
            payload.internal_notes = internalNotes;
        }
        
        if (customerMessage) {
            payload.customer_message = customerMessage;
        }
        
        console.log('Process return request:', payload);
        
        const response = await fetch(`${API_BASE_URL}?action=process`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        console.log('Process return response:', result);
        
        if (!result.success) {
            throw new Error(result.error || 'Unknown error occurred');
        }
        
        return result;
    } catch (error) {
        console.error('Error processing return:', error);
        throw error;
    }
}

// Bulk update returns
async function bulkUpdateReturnsAPI(returnIds, status) {
    try {
        console.log('Bulk update request:', { returnIds, status });
        
        const response = await fetch(`${API_BASE_URL}?action=bulk-update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                return_ids: returnIds,
                status: status
            })
        });
        
        const result = await response.json();
        console.log('Bulk update response:', result);
        
        if (!result.success) {
            throw new Error(result.error || 'Unknown error occurred');
        }
        
        return result;
    } catch (error) {
        console.error('Error bulk updating returns:', error);
        throw error;
    }
}

// ============================================
// UI FUNCTIONS
// ============================================

// Switch between tabs
function switchTab(tabName) {
    // Update active tab
    tabs.forEach(tab => {
        if (tab.getAttribute('data-tab') === tabName) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Update sidebar active state
    sidebarMenuItems.forEach(item => {
        if (item.getAttribute('data-status') === tabName) {
            item.parentElement.classList.add('active');
        } else {
            item.parentElement.classList.remove('active');
        }
    });
    
    // Filter returns
    currentStatusFilter = tabName;
    clearReturnSelection();
    loadReturns(tabName);
}

// Render returns table
function renderReturnsTable(status, returns) {
    returnsBody.innerHTML = '';
    
    // Update table title
    const statusTitles = {
        'all': 'All Returns',
        'pending': 'Pending Returns',
        'processing': 'Processing Returns',
        'approved': 'Approved Returns',
        'rejected': 'Rejected Returns'
    };
    tableTitle.textContent = statusTitles[status] || 'All Returns';
    
    if (returns.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="9" style="text-align: center; padding: 40px; color: #6b7280;">
            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
            No returns found
        </td>`;
        returnsBody.appendChild(row);
        return;
    }
    
    returns.forEach(returnItem => {
        const statusClass = `status-${returnItem.status}`;
        const statusText = returnItem.status.charAt(0).toUpperCase() + returnItem.status.slice(1);
        
        const row = document.createElement('tr');
        
        // Add event listener for row click (to view details)
        row.addEventListener('click', (e) => {
            // Don't trigger if clicking on a button or checkbox
            if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
                openReturnModal(returnItem.id);
            }
        });
        
        row.innerHTML = `
            <td><input type="checkbox" class="return-checkbox" data-return-id="${returnItem.id}"></td>
            <td>${returnItem.id}</td>
            <td>${returnItem.orderId}</td>
            <td>${returnItem.customer}</td>
            <td>${formatDate(returnItem.date)}</td>
            <td>${returnItem.reason}</td>
            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            <td>$${returnItem.orderAmount.toFixed(2)}</td>
            <td>
                <button class="btn btn-primary btn-sm view-return-btn" data-id="${returnItem.id}">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn btn-success btn-sm process-return-btn" data-id="${returnItem.id}">
                    <i class="fas fa-cog"></i> Process
                </button>
            </td>
        `;
        
        returnsBody.appendChild(row);
    });
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.return-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    // Add event listeners to buttons
    document.querySelectorAll('.view-return-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const returnId = button.getAttribute('data-id');
            openReturnModal(returnId);
        });
    });
    
    document.querySelectorAll('.process-return-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const returnId = button.getAttribute('data-id');
            openProcessModal(returnId);
        });
    });
    
    // Update pagination info
    document.getElementById('pagination-info').textContent = `Showing 1-${Math.min(returns.length, 10)} of ${returns.length} returns`;
}

// Filter returns based on current filters
function filterReturns() {
    const dateRange = document.getElementById('date-range').value;
    const customerName = document.getElementById('customer-name').value.toLowerCase();
    const returnId = document.getElementById('return-id').value.toLowerCase();
    const orderId = document.getElementById('order-id').value.toLowerCase();
    const returnReason = document.getElementById('return-reason').value;
    
    let filtered = [...allReturns];
    
    // Filter by customer name
    if (customerName) {
        filtered = filtered.filter(ret => 
            ret.customer.toLowerCase().includes(customerName) ||
            ret.email.toLowerCase().includes(customerName)
        );
    }
    
    // Filter by return ID
    if (returnId) {
        filtered = filtered.filter(ret => 
            ret.id.toLowerCase().includes(returnId)
        );
    }
    
    // Filter by order ID
    if (orderId) {
        filtered = filtered.filter(ret => 
            ret.orderId.toLowerCase().includes(orderId)
        );
    }
    
    // Filter by reason
    if (returnReason !== 'all') {
        filtered = filtered.filter(ret => 
            ret.reason.toLowerCase().includes(returnReason.toLowerCase())
        );
    }
    
    // Filter by date range
    if (dateRange !== 'all') {
        const now = new Date();
        
        filtered = filtered.filter(ret => {
            const retDate = new Date(ret.date);
            
            switch(dateRange) {
                case 'today':
                    return retDate.toDateString() === now.toDateString();
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    return retDate >= weekAgo;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    return retDate >= monthAgo;
                case 'year':
                    return retDate.getFullYear() === now.getFullYear();
                default:
                    return true;
            }
        });
    }
    
    currentReturns = filtered;
    renderReturnsTable(currentStatusFilter, currentReturns);
}

// Clear return selection
function clearReturnSelection() {
    // Uncheck select all
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
    }
    
    // Hide bulk actions
    bulkActions.style.display = 'none';
}

// Open return details modal
async function openReturnModal(returnId) {
    const returnDetails = await loadReturnDetails(returnId);
    
    if (!returnDetails) {
        return;
    }
    
    // Populate modal with return details
    document.getElementById('modal-return-id').textContent = returnDetails.id;
    document.getElementById('customer-name-detail').textContent = returnDetails.customer.name;
    document.getElementById('customer-email-detail').textContent = returnDetails.customer.email;
    document.getElementById('customer-phone-detail').textContent = returnDetails.customer.phone;
    document.getElementById('return-date-detail').textContent = formatDate(returnDetails.date);
    document.getElementById('return-status-detail').textContent = returnDetails.status;
    document.getElementById('order-id-detail').textContent = returnDetails.orderId;
    document.getElementById('order-date-detail').textContent = formatDate(returnDetails.orderDate);
    document.getElementById('order-amount-detail').textContent = `$${returnDetails.orderAmount.toFixed(2)}`;
    document.getElementById('return-address-detail').textContent = returnDetails.customer.address;
    document.getElementById('return-reason-detail').textContent = returnDetails.reason;
    
    // Populate return items
    const itemsContainer = document.getElementById('return-items-detail');
    itemsContainer.innerHTML = '';
    returnDetails.items.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>PRD-${item.productId}</td>
            <td>${item.quantity}</td>
            <td>$${item.price.toFixed(2)}</td>
            <td>${returnDetails.reason}</td>
        `;
        itemsContainer.appendChild(row);
    });
    
    returnModal.style.display = 'flex';
}

// Open process return modal from details
function openProcessModalFromDetails() {
    const returnId = document.getElementById('modal-return-id').textContent;
    const currentStatus = document.getElementById('return-status-detail').textContent;
    
    openProcessModal(returnId, currentStatus);
}

// Open process return modal
function openProcessModal(returnId, currentStatus = null) {
    document.getElementById('process-return-id').textContent = returnId;
    
    if (currentStatus) {
        document.getElementById('process-return-status').value = currentStatus;
    }
    
    // Show/hide form sections based on status
    const status = document.getElementById('process-return-status').value;
    refundAmountGroup.style.display = status === 'Approved' ? 'block' : 'none';
    rejectionReasonGroup.style.display = status === 'Rejected' ? 'block' : 'none';
    
    returnModal.style.display = 'none';
    processModal.style.display = 'flex';
}

// Close all modals
function closeModals() {
    returnModal.style.display = 'none';
    processModal.style.display = 'none';
}

// Process return
async function processReturn(e) {
    e.preventDefault();
    
    const returnId = document.getElementById('process-return-id').textContent;
    const status = document.getElementById('process-return-status').value;
    const refundAmount = document.getElementById('refund-amount').value;
    const rejectionReason = document.getElementById('rejection-reason').value;
    const internalNotes = document.getElementById('internal-notes').value;
    const customerMessage = document.getElementById('customer-message').value;
    
    // Validate form based on action
    if (status === 'Rejected' && !rejectionReason) {
        showNotification('Please provide a rejection reason', 'error');
        return;
    }
    
    if (status === 'Approved' && !refundAmount) {
        showNotification('Please enter a refund amount', 'error');
        return;
    }
    
    try {
        await processReturnAPI(returnId, status, refundAmount, rejectionReason, internalNotes, customerMessage);
        showNotification(`Return ${returnId} has been ${status}`, 'success');
        closeModals();
        refreshReturns();
    } catch (error) {
        showNotification('Error processing return: ' + error.message, 'error');
    }
}

// Toggle filters visibility
function toggleFilters() {
    if (filtersContent.style.display === 'none') {
        filtersContent.style.display = 'grid';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Hide Filters';
    } else {
        filtersContent.style.display = 'none';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Show Filters';
    }
}

// Apply filters
function applyFilters() {
    filterReturns();
    showNotification(`Showing ${currentReturns.length} filtered returns`, 'success');
    
    // Close filters on mobile
    if (window.innerWidth < 768) {
        filtersContent.style.display = 'none';
        toggleFiltersBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Show Filters';
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('date-range').value = 'all';
    document.getElementById('customer-name').value = '';
    document.getElementById('return-id').value = '';
    document.getElementById('order-id').value = '';
    document.getElementById('return-reason').value = 'all';
    
    currentReturns = allReturns;
    renderReturnsTable(currentStatusFilter, currentReturns);
    showNotification('Filters reset!', 'info');
}

// Handle select all checkboxes
function handleSelectAll(e) {
    const isChecked = e.target.checked;
    
    const checkboxes = document.querySelectorAll('.return-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    
    updateBulkActions();
}

// Update bulk actions based on selected items
function updateBulkActions() {
    const selectedCount = document.querySelectorAll('.return-checkbox:checked').length;
    
    if (selectedCount > 0) {
        bulkActions.style.display = 'flex';
        document.getElementById('selected-count').textContent = `${selectedCount} returns selected`;
    } else {
        bulkActions.style.display = 'none';
        selectAllCheckbox.checked = false;
    }
}

// Apply bulk action
async function applyBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const selectedReturns = [];
    
    document.querySelectorAll('.return-checkbox:checked').forEach(checkbox => {
        const returnId = checkbox.getAttribute('data-return-id');
        selectedReturns.push(returnId);
    });
    
    if (!action || selectedReturns.length === 0) {
        showNotification('Please select an action and at least one return', 'error');
        return;
    }
    
    // Confirm bulk action
    if (!confirm(`Are you sure you want to mark ${selectedReturns.length} return(s) as ${action}?`)) {
        return;
    }
    
    try {
        const result = await bulkUpdateReturnsAPI(selectedReturns, action);
        showNotification(result.message, 'success');
        
        // Reset selection
        document.querySelectorAll('.return-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        bulkActions.style.display = 'none';
        
        refreshReturns();
    } catch (error) {
        showNotification('Error applying bulk action: ' + error.message, 'error');
    }
}

// Refresh returns
function refreshReturns() {
    loadReturnsCounts();
    loadReturns(currentStatusFilter);
}

// Export returns
function exportReturns() {
    window.location.href = `${API_BASE_URL}?action=export&status=${currentStatusFilter}`;
    showNotification('Exporting returns...', 'info');
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize the page when loaded
document.addEventListener('DOMContentLoaded', initReturnsPage);