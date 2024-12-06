<?php
require_once '../includes/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendar.php">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Calendar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointments.php">
                            <i class="fas fa-clock me-2"></i>
                            Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patients.php">
                            <i class="fas fa-users me-2"></i>
                            Patients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">
                            <i class="fas fa-tooth me-2"></i>
                            Services
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <i class="fas fa-calendar me-1"></i>
                        This week
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Today's Appointments</h6>
                            <?php
                            $today = date('Y-m-d');
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ?");
                            $stmt->execute([$today]);
                            $todayCount = $stmt->fetchColumn();
                            ?>
                            <h2 class="card-title mb-0"><?php echo $todayCount; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Pending Appointments</h6>
                            <?php
                            $stmt = $conn->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'");
                            $pendingCount = $stmt->fetchColumn();
                            ?>
                            <h2 class="card-title mb-0"><?php echo $pendingCount; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Total Patients</h6>
                            <?php
                            $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'patient'");
                            $patientCount = $stmt->fetchColumn();
                            ?>
                            <h2 class="card-title mb-0"><?php echo $patientCount; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Completed Today</h6>
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND status = 'completed'");
                            $stmt->execute([$today]);
                            $completedToday = $stmt->fetchColumn();
                            ?>
                            <h2 class="card-title mb-0"><?php echo $completedToday; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <h3 class="mb-3">Recent Appointments</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->query("
                            SELECT a.*, u.firstname, u.lastname, s.name as service_name 
                            FROM appointments a 
                            JOIN users u ON a.patient_id = u.id 
                            JOIN services s ON a.service_id = s.id 
                            ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                            LIMIT 10
                        ");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($row['status']) {
                                        'pending' => 'warning',
                                        'confirmed' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="updateStatus(<?php echo $row['id']; ?>, 'confirmed')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" 
                                            onclick="updateStatus(<?php echo $row['id']; ?>, 'completed')">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="updateStatus(<?php echo $row['id']; ?>, 'cancelled')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
