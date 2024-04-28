/**
 * Conditionally renders pages of the navigation bar 
 * based on whether the user is logged in or if they
 * are a regular user or an admin.
 */
document.addEventListener('DOMContentLoaded', function () {
    let navMenu = document.getElementById('navigation-menu');
    let links = []; 

    if (userLoggedIn) {
        // Links for logged-in users 
        links.push('<li' + (typeof index !== 'undefined' && index ? ' class="active"' : '') + '><a href="index.php">Home</a></li>');
        links.push('<li' + (typeof profile !== 'undefined' && profile ? ' class="active"' : '') + '><a href="profile.php">Profile</a></li>');
        
        if (typeof userIsAdmin !== 'undefined' && userIsAdmin) {
            links.push('<li' + (typeof userList !== 'undefined' && userList ? ' class="active"' : '') + '><a href="user_list.php">User List</a></li>');
        }

        links.push('<li><a href="logout.php">Log Out</a></li>');
    } else {
        // Links for non-logged-in users
        links.push('<li><a href="index.php">Home</a></li>');
        links.push('<li' + (typeof login !== 'undefined' && login ? ' class="active"' : '') + '><a href="login.php">Login</a></li>');
        links.push('<li' + (typeof register !== 'undefined' && register ? ' class="active"' : '') + '><a href="register.php">Register</a></li>');
    }

    // joining all the link HTML together and setting it as the innerHTML of the navMenu
    navMenu.innerHTML = links.join('');
});
