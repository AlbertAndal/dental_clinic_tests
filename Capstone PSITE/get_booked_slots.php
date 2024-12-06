<?php
require_once 'includes/config.php';
require_once 'includes/session_check.php';

// Ensure user is logged in
requireLogin();

$date = $_GET['date'] ?? date('Y-m-d');

try {
    // Get all appointments for the selected date
    $stmt = $conn->prepare("
        SELECT TIME_FORMAT(appointment_time, '%H:%i') as start_time,
               TIME_FORMAT(
                   DATE_ADD(appointment_time, INTERVAL (
                       SELECT duration FROM services WHERE id = appointments.service_id
                   ) MINUTE), 
                   '%H:%i'
               ) as end_time
        FROM appointments
        WHERE DATE(appointment_time) = ?
        AND status IN ('pending', 'confirmed')
    ");
    
    $stmt->execute([$date]);
    $bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($bookedSlots);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch booked slots']);
}
?>
