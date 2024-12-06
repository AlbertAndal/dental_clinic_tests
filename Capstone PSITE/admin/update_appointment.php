<?php
require_once '../includes/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $appointment_id = $_POST['appointment_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $start = $_POST['start'] ?? null;
        $end = $_POST['end'] ?? null;

        if ($appointment_id && $status) {
            // Update appointment status
            $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->execute([$status, $appointment_id]);
        }

        if ($appointment_id && $start) {
            // Update appointment date and time
            $date = date('Y-m-d', strtotime($start));
            $time = date('H:i:s', strtotime($start));
            
            $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ? WHERE id = ?");
            $stmt->execute([$date, $time, $appointment_id]);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);

    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method']);
}
?>
