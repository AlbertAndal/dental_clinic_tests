<?php
require_once 'includes/config.php';
require_once 'includes/session_check.php';

// Ensure user is logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    try {
        // Update user profile
        $stmt = $conn->prepare("
            UPDATE users 
            SET firstname = ?, lastname = ?, phone = ? 
            WHERE id = ?
        ");
        
        $stmt->execute([$firstname, $lastname, $phone, $_SESSION['user_id']]);
        
        // Update session name
        $_SESSION['name'] = $firstname . ' ' . $lastname;
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to update profile']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
