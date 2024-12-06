<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dental Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c7be5;
            --secondary-color: #6e84a3;
            --success-color: #00d97e;
            --danger-color: #e63757;
            --warning-color: #f6c343;
            --info-color: #39afd1;
        }

        body {
            background-color: #f9fbfd;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 90px 0 0;
            width: 260px;
            background: white;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.05);
        }

        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 90px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .navbar {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.05);
            background: white !important;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            margin: 0.25rem 1rem;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: rgba(44, 123, 229, 0.1);
        }

        .nav-link i {
            width: 1.25rem;
            text-align: center;
            margin-right: 0.75rem;
        }

        main {
            margin-left: 260px;
            padding: 90px 1.5rem 1.5rem;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.05);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3ebf6;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #1a68d1;
            border-color: #1a68d1;
        }

        .table th {
            font-weight: 500;
            color: var(--secondary-color);
        }

        .badge {
            padding: 0.5em 1em;
        }

        /* Status colors */
        .status-pending { background-color: var(--warning-color); }
        .status-confirmed { background-color: var(--success-color); }
        .status-cancelled { background-color: var(--danger-color); }
        .status-completed { background-color: var(--info-color); }

        /* Charts */
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-light bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                Dental Clinic
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                       href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : ''; ?>" 
                       href="appointments.php">
                        <i class="fas fa-calendar-check"></i>
                        Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'calendar.php' ? 'active' : ''; ?>" 
                       href="calendar.php">
                        <i class="fas fa-calendar-alt"></i>
                        Calendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'patients.php' ? 'active' : ''; ?>" 
                       href="patients.php">
                        <i class="fas fa-users"></i>
                        Patients
                    </a>
                </li>
                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" 
                       href="services.php">
                        <i class="fas fa-tooth"></i>
                        Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : ''; ?>" 
                       href="staff.php">
                        <i class="fas fa-user-md"></i>
                        Staff
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" 
                       href="reports.php">
                        <i class="fas fa-chart-bar"></i>
                        Reports
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main role="main" class="pb-3">
