<?php
require_once '../../db/grinyard_db.php';

// Fetch extension officers from database
$query = "SELECT * FROM extension_officers ORDER BY created_at DESC";
$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extension Officers Management - Farm Extension Services</title>
    <link rel="stylesheet" href="../../assets/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Farm Extension</h2>
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="farmers.php"><i class="fas fa-users"></i> Farmers</a>
            <a href="officers.php" class="active"><i class="fas fa-user-tie"></i> Extension Officers</a>
            <a href="appointments_manage.php"><i class="fas fa-calendar-check"></i> Appointments</a>
            <a href="../../logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="dashboard-header">
            <h1><i class="fas fa-user-tie"></i> Extension Officers Management</h1>
        </header>

        <main class="container-fluid mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Extension Officers</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficerModal">
                        <i class="fas fa-plus"></i> Add New Officer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Specialization</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($officer = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($officer['email']); ?></td>
                                    <td><?php echo htmlspecialchars($officer['specialization']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($officer['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-officer" data-id="<?php echo $officer['extension_officer_id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary edit-officer" data-id="<?php echo $officer['extension_officer_id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-officer" data-id="<?php echo $officer['extension_officer_id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Officer Modal -->
    <div class="modal fade" id="addOfficerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Extension Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addOfficerForm">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" class="form-control" name="specialization" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveOfficer">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add Officer
            $('#saveOfficer').click(function() {
                const formData = new FormData($('#addOfficerForm')[0]);
                
                $.ajax({
                    url: '../../actions/add_officer.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            location.reload();
                        } else {
                            alert('Failed to add officer: ' + result.message);
                        }
                    },
                    error: function() {
                        alert('Error occurred while adding officer');
                    }
                });
            });

            // Delete Officer
            $('.delete-officer').click(function() {
                if (confirm('Are you sure you want to delete this officer?')) {
                    const officerId = $(this).data('id');
                    
                    $.post('../../actions/delete_officer.php', {
                        officer_id: officerId
                    }, function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete officer: ' + result.message);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
