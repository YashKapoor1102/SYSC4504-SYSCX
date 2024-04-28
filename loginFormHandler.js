/**
 * Whenever the user types into the email and password field
 * on the login.php page, it gives the user immediate feedback after
 * the "Login" button is pressed
 * whether the email already exists in the database and 
 * if the password of that particular user is valid.
 */
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); 

    const formData = new FormData(this);
    let queryString = new URLSearchParams(formData).toString(); 
    let validateCredentials = new XMLHttpRequest();
    validateCredentials.open('POST', 'login.php', true);
    validateCredentials.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    validateCredentials.onload = function() {
        if (this.status >= 200 && this.status < 300) {
            let response = JSON.parse(this.responseText);

            if (response.success) {
                // Redirecting to index.php on success
                window.location.href = 'index.php'; 
            } else {
                document.getElementById('error_message_password').textContent = response.message;
            }
        } else {
            console.error('This request has failed.'); 
        }
    };

    validateCredentials.onerror = function() {
        console.error('This request has failed.'); 
    };

    validateCredentials.send(queryString);
});