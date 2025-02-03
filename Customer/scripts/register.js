document.getElementById('registration-form').addEventListener('submit', function(event) {
    let isValid = true;

    // Clear previous error messages
    const errorElements = document.querySelectorAll('.error');
    errorElements.forEach((el) => el.textContent = '');

    // Validate username
    const username = document.getElementById('username');
    if (username.value.trim() === '') {
        document.getElementById('username-error').textContent = 'Username is required.';
        isValid = false;
    }

    // Validate email
    const email = document.getElementById('email');
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!emailPattern.test(email.value.trim())) {
        document.getElementById('email-error').textContent = 'Please enter a valid email.';
        isValid = false;
    }

    // Validate password
    const password = document.getElementById('password');
    if (password.value.trim() === '') {
        document.getElementById('password-error').textContent = 'Password is required.';
        isValid = false;
    }

    // Validate full name
    const fullName = document.getElementById('full_name');
    if (fullName.value.trim() === '') {
        document.getElementById('full_name-error').textContent = 'Full Name is required.';
        isValid = false;
    }

    // Validate phone
    const phone = document.getElementById('phone');
    if (phone.value.trim() === '') {
        document.getElementById('phone-error').textContent = 'Phone number is required.';
        isValid = false;
    }

    // Validate billing address
    const billingAddress = document.getElementById('billing_address');
    if (billingAddress.value.trim() === '') {
        document.getElementById('billing_address-error').textContent = 'Billing address is required.';
        isValid = false;
    }

    // Validate shipping address
    const shippingAddress = document.getElementById('shipping_address');
    if (shippingAddress.value.trim() === '') {
        document.getElementById('shipping_address-error').textContent = 'Shipping address is required.';
        isValid = false;
    }

    // If not valid, prevent form submission
    if (!isValid) {
        event.preventDefault();
    }
});
