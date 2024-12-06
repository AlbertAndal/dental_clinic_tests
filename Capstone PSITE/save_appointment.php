<?php
require_once 'includes/config.php';
require_once 'includes/session_check.php';

// Ensure user is logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;
    $appointment_date = $_POST['appointment_date'] ?? null;
    $appointment_time = $_POST['appointment_time'] ?? null;
    $notes = $_POST['notes'] ?? '';
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Check if slot is still available
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE DATE(a.appointment_time) = ?
            AND TIME(a.appointment_time) <= ?
            AND DATE_ADD(a.appointment_time, INTERVAL s.duration MINUTE) >= ?
            AND a.status IN ('pending', 'confirmed')
        ");
        
        $datetime = $appointment_date . ' ' . $appointment_time;
        $stmt->execute([$appointment_date, $appointment_time, $datetime]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            throw new Exception('This time slot is no longer available');
        }
        
        // Insert appointment
        $stmt = $conn->prepare("
            INSERT INTO appointments (
                patient_id, service_id, appointment_time, notes, status, created_at
            ) VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $service_id,
            $datetime,
            $notes
        ]);
        
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        $conn->rollBack();
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
?>
