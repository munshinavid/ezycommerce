// Update the event listener attachment
document.addEventListener('DOMContentLoaded', () => {
    // Initialize with correct class names
    document.querySelectorAll('.update-btn').forEach(button => {
        button.addEventListener('click', handleStatusUpdate);
    });

    // Store original status values
    document.querySelectorAll('tr[data-order-id]').forEach(row => {
        const statusSelect = row.querySelector('.status-select');
        row.dataset.originalStatus = statusSelect.value;
    });
});



// JS/order-updates.js
document.addEventListener('DOMContentLoaded', initOrderUpdates);

function initOrderUpdates() {
    // Store original status values
    document.querySelectorAll('tr[data-order-id]').forEach(row => {
        const statusSelect = row.querySelector('.statusSelect');
        row.dataset.originalStatus = statusSelect.value;
    });

    // Add event listeners
    document.querySelectorAll('.updateBtn').forEach(button => {
        button.addEventListener('click', handleStatusUpdate);
    });
}

async function handleStatusUpdate(e) {
    const row = e.target.closest('tr');
    const orderId = row.dataset.orderId;
    const statusSelect = row.querySelector('.statusSelect');
    const status = statusSelect.value;

    try {
        const response = await fetch('../control/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                status: status
            })
        });

        const data = await response.json();
        
        if (data.success) {
            updateUIStatus(row, status);
            showFeedback('Status updated successfully!', 'success');
        } else {
            revertStatus(statusSelect, row);
            showFeedback('Error: ' + (data.error || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        revertStatus(statusSelect, row);
        showFeedback('Failed to update status. Please try again.', 'error');
    }
}

function updateUIStatus(row, status) {
    const statusCell = row.querySelector('td:nth-child(5)');
    statusCell.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    row.dataset.originalStatus = status;
}

function revertStatus(selectElement, row) {
    selectElement.value = row.dataset.originalStatus;
}

function showFeedback(message, type = 'info') {
    const alertBox = document.createElement('div');
    alertBox.className = `feedback ${type}`;
    alertBox.textContent = message;
    
    document.body.appendChild(alertBox);
    setTimeout(() => alertBox.remove(), 3000);
}