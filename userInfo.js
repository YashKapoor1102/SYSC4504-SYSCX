document.addEventListener('DOMContentLoaded', function() {
    if (userLoggedIn) {
        document.getElementById('userFullName').textContent = userFullName;
        document.getElementById('userAvatar').src = userAvatar;
        document.getElementById('userAvatar').alt = "Avatar";
        document.getElementById('userEmail').textContent = userEmail;
        document.getElementById('userEmail').href = "mailto:" + userEmail;
        document.getElementById('userProgram').textContent = userProgram;
    } else {
        // hide the user info section.
        document.getElementById('user-info').style.display = 'none';
    }
});
