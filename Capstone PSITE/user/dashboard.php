<?php
require_once '../includes/config.php';
require_once '../includes/session_check.php';

// Ensure user is logged in and is a patient
requireLogin();
if ($_SESSION['role'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

// Get user's appointments
$stmt = $conn->prepare("
    SELECT 
        a.*,
        s.name as service_name,
        s.duration,
        s.price
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$total_appointments = count($appointments);
$completed = 0;
$upcoming = 0;
$cancelled = 0;

foreach ($appointments as $appointment) {
    switch ($appointment['status']) {
        case 'completed':
            $completed++;
            break;
        case 'pending':
        case 'confirmed':
            if (strtotime($appointment['appointment_time']) > time()) {
                $upcoming++;
            }
            break;
        case 'cancelled':
            $cancelled++;
            break;
    }
}

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">My Dashboard</h2>
            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="../book-appointment.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Book New Appointment
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Appointments</h6>
                    <h3 class="mb-0"><?php echo $total_appointments; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Upcoming</h6>
                    <h3 class="mb-0 text-primary"><?php echo $upcoming; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Completed</h6>
                    <h3 class="mb-0 text-success"><?php echo $completed; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Cancelled</h6>
                    <h3 class="mb-0 text-danger"><?php echo $cancelled; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Upcoming Appointments</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $hasUpcoming = false;
                        foreach ($appointments as $appointment) {
                            if (($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed') && 
                                strtotime($appointment['appointment_time']) > time()) {
                                $hasUpcoming = true;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                    <td>
                                        <?php 
                                        $date = new DateTime($appointment['appointment_time']);
                                        echo $date->format('M j, Y g:i A'); 
                                        ?>
                                    </td>
                                    <td><?php echo $appointment['duration']; ?> mins</td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'completed' => 'info',
                                            'cancelled' => 'danger'
                                        ][$appointment['status']];
                                        ?>
                                        <span class="badge bg-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($appointment['status'] != 'cancelled') { ?>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="cancelAppointment(<?php echo $appointment['id']; ?>)">
                                                Cancel
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        if (!$hasUpcoming) {
                            echo '<tr><td colspan="5" class="text-center py-4">No upcoming appointments</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Appointment History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Appointment History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $hasHistory = false;
                        foreach ($appointments as $appointment) {
                            if ($appointment['status'] == 'completed' || $appointment['status'] == 'cancelled' || 
                                (($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed') && 
                                 strtotime($appointment['appointment_time']) <= time())) {
                                $hasHistory = true;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                    <td>
                                        <?php 
                                        $date = new DateTime($appointment['appointment_time']);
                                        echo $date->format('M j, Y g:i A'); 
                                        ?>
                                    </td>
                                    <td><?php echo $appointment['duration']; ?> mins</td>
                                    <td>$<?php echo number_format($appointment['price'], 2); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'completed' => 'info',
                                            'cancelled' => 'danger'
                                        ][$appointment['status']];
                                        ?>
                                        <span class="badge bg-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        if (!$hasHistory) {
                            echo '<tr><td colspan="5" class="text-center py-4">No appointment history</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        fetch('../cancel_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'appointment_id=' + appointmentId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to cancel appointment');
            }
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>
