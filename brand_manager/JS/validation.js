document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form');

    form.addEventListener('submit', (event) => {
        // Prevent default form submission
        event.preventDefault();

        // Clear previous error messages
        document.getElementById('firstname-error').innerHTML = '';
        document.getElementById('email-error').innerHTML = '';
        document.getElementById('password-error').innerHTML = '';
        document.getElementById('repeat-password-error').innerHTML = '';

        // Get form input values
        const firstname = document.getElementById('firstname-input').value.trim();
        const email = document.getElementById('email-input').value.trim();
        const password = document.getElementById('password-input').value;
        const repeatPassword = document.getElementById('repeat-password-input').value;

        let isValid = true;

        // Validate firstname
        if (firstname === '') {
            document.getElementById('firstname-error').innerHTML = 'Firstname is required.';
            isValid = false;
        } else if (firstname.length < 3) {
            document.getElementById('firstname-error').innerHTML = 'Must be at least 3 characters.';
            isValid = false;
        }

        // Validate email
        if (email === '') {
            document.getElementById('email-error').innerHTML = 'Email is required.';
            isValid = false;
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email-error').innerHTML = 'Invalid email format.';
                isValid = false;
            }
        }

        // Validate password
        if (password === '') {
            document.getElementById('password-error').innerHTML = 'Password is required.';
            isValid = false;
        } else if (password.length < 8) {
            document.getElementById('password-error').innerHTML = 'At least 8 characters required.';
            isValid = false;
        } else if (!/[A-Z]/.test(password)) {
            document.getElementById('password-error').innerHTML = 'At least one uppercase letter.';
            isValid = false;
        } else if (!/[a-z]/.test(password)) {
            document.getElementById('password-error').innerHTML = 'At least one lowercase letter.';
            isValid = false;
        } else if (!/[0-9]/.test(password)) {
            document.getElementById('password-error').innerHTML = 'At least one number.';
            isValid = false;
        } else if (!/[!@#$%^&*]/.test(password)) {
            document.getElementById('password-error').innerHTML = 'At least one special character (!@#$%^&*).';
            isValid = false;
        }

        // Validate repeat password
        if (repeatPassword === '') {
            document.getElementById('repeat-password-error').innerHTML = 'Repeat Password is required.';
            isValid = false;
        } else if (password !== repeatPassword) {
            document.getElementById('repeat-password-error').innerHTML = 'Passwords do not match.';
            isValid = false;
        }

        // Submit form if valid
        if (isValid) {
            form.submit();
        }
    });
});