<?php
require_once '../includes/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $stmt = $conn->query("
        SELECT 
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            u.firstname,
            u.lastname,
            s.name as service_name,
            s.duration
        FROM appointments a
        JOIN users u ON a.patient_id = u.id
        JOIN services s ON a.service_id = s.id
        ORDER BY a.appointment_date, a.appointment_time
    ");

    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Combine date and time
        $start = $row['appointment_date'] . ' ' . $row['appointment_time'];
        
        // Calculate end time based on service duration
        $end = date('Y-m-d H:i:s', strtotime($start . ' + ' . $row['duration'] . ' minutes'));

        // Set color based on status
        $color = match($row['status']) {
            'pending' => '#ffc107',    // warning yellow
            'confirmed' => '#0d6efd',  // primary blue
            'completed' => '#198754',  // success green
            'cancelled' => '#dc3545',  // danger red
            default => '#6c757d'       // secondary gray
        };

        $events[] = [
            'id' => $row['id'],
            'title' => $row['firstname'] . ' ' . $row['lastname'] . ' - ' . $row['service_name'],
            'start' => $start,
            'end' => $end,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'status' => $row['status'],
                'service' => $row['service_name'],
                'duration' => $row['duration']
            ]
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($events);

} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>
