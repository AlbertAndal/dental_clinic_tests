<?php
require_once 'includes/config.php';
require_once 'includes/session_check.php';

// Ensure user is logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? null;
    
    try {
        // Verify appointment belongs to user
        $stmt = $conn->prepare("
            SELECT id 
            FROM appointments 
            WHERE id = ? AND patient_id = ? AND status IN ('pending', 'confirmed')
        ");
        $stmt->execute([$appointment_id, $_SESSION['user_id']]);
        
        if (!$stmt->fetch()) {
            echo json_encode(['error' => 'Invalid appointment']);
            exit;
        }
        
        // Cancel appointment
        $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$appointment_id]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to cancel appointment']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
