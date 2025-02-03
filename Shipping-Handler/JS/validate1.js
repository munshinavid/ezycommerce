function validateForm() {
    const email = document.getElementById("uname").value.trim();
    const password = document.getElementById("pass").value.trim();

    // Regex for validating email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let isValid = true;

    // Email Validation
    if (!emailRegex.test(email)) {
        document.getElementById("error").innerHTML = "Enter a valid email address.";
        isValid = false;
    } else {
        document.getElementById("error").innerHTML = "";
    }

    // Password Validation
    if (password.length < 8) {
        document.getElementById("passError").innerHTML = "Password must be at least 8 characters long.";
        isValid = false;
    } else {
        document.getElementById("passError").innerHTML = "";
    }

    return isValid; // Return false if any validation fails
}
