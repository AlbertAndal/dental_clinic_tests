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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="calendar.php">
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
                <h1 class="h2">Calendar</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="view-day">Day</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="view-week">Week</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="view-month">Month</button>
                    </div>
                </div>
            </div>

            <!-- Calendar Container -->
            <div id="calendar"></div>
        </main>
    </div>
</div>

<!-- Add FullCalendar CSS and JS -->
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.8/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/main.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        allDaySlot: false,
        slotDuration: '00:30:00',
        events: 'get_appointments.php',
        editable: true,
        eventClick: function(info) {
            // Handle appointment click
            showAppointmentDetails(info.event);
        },
        eventDrop: function(info) {
            // Handle appointment drag and drop
            updateAppointmentTime(info.event);
        }
    });
    calendar.render();

    // View buttons functionality
    document.getElementById('view-day').addEventListener('click', function() {
        calendar.changeView('timeGridDay');
        updateActiveButton(this);
    });
    
    document.getElementById('view-week').addEventListener('click', function() {
        calendar.changeView('timeGridWeek');
        updateActiveButton(this);
    });
    
    document.getElementById('view-month').addEventListener('click', function() {
        calendar.changeView('dayGridMonth');
        updateActiveButton(this);
    });
});

function updateActiveButton(clickedButton) {
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    clickedButton.classList.add('active');
}

function showAppointmentDetails(event) {
    // Implement appointment details modal
}

function updateAppointmentTime(event) {
    // Implement appointment time update via AJAX
}
</script>

<style>
#calendar {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.fc-event {
    border-radius: 3px;
    font-size: 0.85em;
    cursor: pointer;
}

.fc-event:hover {
    opacity: 0.9;
}

.fc-toolbar-title {
    font-size: 1.5em !important;
    font-weight: 500;
}

.fc-button-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.fc-button-primary:hover {
    background-color: #2567c3 !important;
    border-color: #2567c3 !important;
}

.fc-timegrid-slot {
    height: 40px !important;
}
</style>

<?php include '../includes/footer.php'; ?>
