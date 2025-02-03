document.getElementById("registration-form").addEventListener("submit", function(event) {
    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    var errorMessage = document.getElementById("js-error-message");

    // Clear previous error messages
    errorMessage.textContent = "";

    // Validate if all fields are filled
    if (username === "" || email === "" || password === "" || confirmPassword === "") {
        event.preventDefault(); // Prevent form submission
        errorMessage.textContent = "All fields are required. Please fill in all fields.";
        return; // Stop further checks if any field is empty
    }

    // Validate if password and confirm password match
    if (password !== confirmPassword) {
        event.preventDefault(); // Prevent form submission
        errorMessage.textContent = "Passwords do not match. Please try again.";
    }
});
