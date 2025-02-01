// JavaScript code to handle toggling between orders table and order items table

// This will simulate the order details and the items for each order.
const orderDetails = {
    12345: {
        total: "$99.99",
        status: "Delivered",
        items: [
            { productName: "Product A", quantity: 2, price: "$30.00", total: "$60.00" },
            { productName: "Product B", quantity: 1, price: "$39.99", total: "$39.99" },
        ]
    },
    12346: {
        total: "$49.99",
        status: "Shipped",
        items: [
            { productName: "Product C", quantity: 1, price: "$49.99", total: "$49.99" }
        ]
    }
};

// Function to show order details and hide the orders table
function showOrderDetails(orderId) {
    const order = orderDetails[orderId];

    // Hide the orders section
    document.getElementById('order-history-section').style.display = 'none';
    
    // Show the order items section
    document.getElementById('order-items-section').style.display = 'block';

    // Fill the order items section
    let itemsHTML = '';
    order.items.forEach(item => {
        itemsHTML += `
            <tr>
                <td>${item.productName}</td>
                <td>${item.quantity}</td>
                <td>${item.price}</td>
                <td>${item.total}</td>
            </tr>
        `;
    });
    document.getElementById('order-items-list').innerHTML = itemsHTML;

    // Optionally, fill in the order details modal
    document.getElementById('order-id').innerText = orderId;
    document.getElementById('order-total').innerText = order.total;
    document.getElementById('order-status').innerText = order.status;
}

// Function to go back to the orders list
function backToOrders() {
    document.getElementById('order-history-section').style.display = 'block';
    document.getElementById('order-items-section').style.display = 'none';
}

// Close the order details modal (optional)
function closeOrderDetails() {
    document.getElementById('order-details-modal').style.display = 'none';
}
