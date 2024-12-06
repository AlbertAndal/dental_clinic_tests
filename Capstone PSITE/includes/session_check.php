<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Check session timeout (30 minutes)
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes in seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        // Session has expired
        session_unset();
        session_destroy();
        header('Location: /Capstone%20PSITE/login.php?msg=session_expired');
        exit();
    }
    $_SESSION['last_activity'] = time();
}

// Update last activity time
if (isLoggedIn()) {
    checkSessionTimeout();
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /Capstone%20PSITE/login.php');
        exit();
    }
}

// Function to require admin access
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /Capstone%20PSITE/index.php?error=unauthorized');
        exit();
    }
}

// Function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Function to get current user role
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// Function to get current user name
function getCurrentUserName() {
    return $_SESSION['name'] ?? null;
}
?>
