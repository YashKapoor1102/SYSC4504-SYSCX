
/**
 * Checks if the user entered valid input by
 * ensuring the "Confirm Password" field is equal to 
 * the "Password" field on the register page.
 * 
 * If both fields are not equal to one another, it displays the appropriate error message.
 */
document.querySelector('form').addEventListener('submit', function(event) {
    let password = document.getElementById('password').value;
    let confirmPassword = document.getElementById('confirm_password').value;
    if (password !== confirmPassword) {
        document.getElementById('error_message_password').textContent = "Passwords do not match.";
        event.preventDefault();
    } else {
        document.getElementById('error_message_password').textContent = "";
    }
});

