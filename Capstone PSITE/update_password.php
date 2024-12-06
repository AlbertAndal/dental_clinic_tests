<?php
require_once 'includes/config.php';
require_once 'includes/session_check.php';

// Ensure user is logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($current_password, $user['password'])) {
            echo json_encode(['error' => 'Current password is incorrect']);
            exit;
        }
        
        // Validate new password
        if (strlen($new_password) < 6) {
            echo json_encode(['error' => 'New password must be at least 6 characters long']);
            exit;
        }
        
        if ($new_password !== $confirm_password) {
            echo json_encode(['error' => 'New passwords do not match']);
            exit;
        }
        
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([
            password_hash($new_password, PASSWORD_DEFAULT),
            $_SESSION['user_id']
        ]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to update password']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
