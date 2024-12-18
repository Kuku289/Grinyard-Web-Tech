<?php
session_start();
require_once '../../db/config.php';

// Get counts from database
$query_farmers = "SELECT COUNT(*) as count FROM farmers";
$query_officers = "SELECT COUNT(*) as count FROM extension_officers";
$query_appointments = "SELECT COUNT(*) as count FROM appointments";
$query_pending = "SELECT COUNT(*) as count FROM appointments WHERE status = 'Pending'";

$total_farmers = $conn->query($query_farmers)->fetch_assoc()['count'];
$total_officers = $conn->query($query_officers)->fetch_assoc()['count'];
$total_appointments = $conn->query($query_appointments)->fetch_assoc()['count'];
$pending_appointments = $conn->query($query_pending)->fetch_assoc()['count'];

// Get recent appointments
$query_recent = "SELECT a.*, f.first_name as farmer_fname, f.last_name as farmer_lname,
                e.first_name as officer_fname, e.last_name as officer_lname
                FROM appointments a
                LEFT JOIN farmers f ON a.farmer_id = f.farmer_id
                LEFT JOIN extension_officers e ON a.officer_id = e.extension_officer_id
                ORDER BY a.created_at DESC LIMIT 5";
$recent_appointments = $conn->query($query_recent);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Extension Services - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Farm Extension</h2>
        <nav>
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="farmers.php"><i class="fas fa-users"></i> Farmers</a>
            <a href="officers.php"><i class="fas fa-user-tie"></i> Extension Officers</a>
            <a href="appointments_manage.php"><i class="fas fa-calendar-check"></i> Appointments</a>
            <a href="../../logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        </header>
    
        <main>
            <!-- Analytics Section -->
            <section class="analytics">
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-users"></i> Total Farmers</h3>
                        <p class="number"><?php echo $total_farmers; ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-user-tie"></i> Extension Officers</h3>
                        <p class="number"><?php echo $total_officers; ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-calendar-check"></i> Total Appointments</h3>
                        <p class="number"><?php echo $total_appointments; ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fas fa-clock"></i> Pending Appointments</h3>
                        <p class="number"><?php echo $pending_appointments; ?></p>
                    </div>
                </div>
            </section>

            <!-- Recent Appointments Section -->
            <section class="recent-appointments mt-4">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Recent Appointments</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Farmer</th>
                                        <th>Extension Officer</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($app = $recent_appointments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($app['appointment_date'])); ?></td>
                                        <td><?php echo $app['farmer_fname'] . ' ' . $app['farmer_lname']; ?></td>
                                        <td><?php echo $app['officer_fname'] . ' ' . $app['officer_lname']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $app['status'] === 'Pending' ? 'warning' : 
                                                    ($app['status'] === 'Confirmed' ? 'primary' : 
                                                    ($app['status'] === 'Completed' ? 'success' : 'danger')); 
                                            ?>">
                                                <?php echo $app['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="appointments_manage.php" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
