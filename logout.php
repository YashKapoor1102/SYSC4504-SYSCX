<?php
    session_start();

    // unsetting all session variables
    $_SESSION = array();
    session_destroy();

    // redirecting the user to login.php
    header("Location: login.php");
    exit();
?>