/**
 * Whenever the user types into the email field
 * on the register.php page, it gives the user immediate feedback
 * whether the email already exists in the database.
 */
let emailExists = false;
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('email').addEventListener('input', function() {
        const email = this.value;
        if (email.length > 0) {
            let validateEmail = new XMLHttpRequest();
            validateEmail.open('POST', 'register.php', true);
            validateEmail.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            validateEmail.onload = function() {
                if (this.status >= 200 && this.status < 300) {
                    let response = JSON.parse(this.responseText);
                    if (response.exists) {
                        document.getElementById('email-error').textContent = "This email already exists. Please use another email.";
                        emailExists = true; 
                    } else {
                        document.getElementById('email-error').textContent = "";
                        emailExists = false; 
                    }
                }
            };
            validateEmail.onerror = function() {
                console.error('This request has failed.');
            };
            validateEmail.send('action=check_email&email=' + encodeURIComponent(email));
        }
    });

    document.querySelector('form').addEventListener('submit', function(event) {
        if (emailExists) {
            event.preventDefault(); 
        }
    });
});
