<?php
require_once 'includes/config.php';
require_once 'includes/session_check.php';

// Ensure user is logged in
requireLogin();

// Get user information
$stmt = $conn->prepare("
    SELECT firstname, lastname, email, phone, created_at 
    FROM users 
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's appointments with service details
$stmt = $conn->prepare("
    SELECT 
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        a.notes,
        s.name as service_name,
        s.duration,
        s.price
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mb-3">
                            <span class="avatar-initials">
                                <?php 
                                echo strtoupper(substr($user['firstname'], 0, 1) . 
                                              substr($user['lastname'], 0, 1)); 
                                ?>
                            </span>
                        </div>
                        <h4 class="mb-0"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h4>
                        <p class="text-muted">Patient</p>
                    </div>

                    <div class="border-top pt-3">
                        <div class="mb-3">
                            <label class="small text-muted">Email</label>
                            <div><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Phone</label>
                            <div><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Member Since</label>
                            <div><?php echo date('F d, Y', strtotime($user['created_at'])); ?></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </div>
            </div>

            <!-- Appointment Statistics -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Appointment Statistics</h5>
                    <?php
                    $totalAppointments = count($appointments);
                    $completedAppointments = count(array_filter($appointments, function($a) {
                        return $a['status'] === 'completed';
                    }));
                    $upcomingAppointments = count(array_filter($appointments, function($a) {
                        return $a['status'] === 'confirmed' && strtotime($a['appointment_date']) >= strtotime('today');
                    }));
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Appointments</span>
                        <span class="fw-bold"><?php echo $totalAppointments; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Completed</span>
                        <span class="fw-bold text-success"><?php echo $completedAppointments; ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Upcoming</span>
                        <span class="fw-bold text-primary"><?php echo $upcomingAppointments; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Appointment History</h4>
                    
                    <?php if (empty($appointments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="mb-0">No appointments found</p>
                            <a href="book-appointment.php" class="btn btn-primary mt-3">Book Your First Appointment</a>
                        </div>
                    <?php else: ?>
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
                                    <?php foreach ($appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                            <td>
                                                <?php 
                                                echo date('M d, Y', strtotime($appointment['appointment_date'])) . '<br>';
                                                echo date('h:i A', strtotime($appointment['appointment_time']));
                                                ?>
                                            </td>
                                            <td><?php echo $appointment['duration']; ?> mins</td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($appointment['status']) {
                                                        'pending' => 'warning',
                                                        'confirmed' => 'primary',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo ucfirst($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                                                    <?php if (strtotime($appointment['appointment_date']) > strtotime('today')): ?>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="cancelAppointment(<?php echo $appointment['id']; ?>)">
                                                            Cancel
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="firstname" 
                               value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lastname" 
                               value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateProfile()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updatePassword()">Change Password</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background-color: var(--primary-color);
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initials {
    color: white;
    font-size: 2.5rem;
    font-weight: 500;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
</style>

<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        fetch('cancel_appointment.php', {
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

function updateProfile() {
    const form = document.getElementById('editProfileForm');
    const formData = new FormData(form);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to update profile');
        }
    });
}

function updatePassword() {
    const form = document.getElementById('changePasswordForm');
    const formData = new FormData(form);

    fetch('update_password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password updated successfully');
            $('#changePasswordModal').modal('hide');
            form.reset();
        } else {
            alert(data.error || 'Failed to update password');
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
