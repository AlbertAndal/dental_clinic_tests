<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Clear any other cookies set by the application
setcookie('remember_me', '', time()-3600, '/');

// Redirect to login page with a logged out message
header('Location: login.php?msg=logged_out');
exit();
?>
