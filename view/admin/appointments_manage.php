<?php
require_once '../../db/grinyard_db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Appointments</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Farmer</th>
                        <th>Extension Officer</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="appointmentsTable">
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Appointment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select class="form-select" id="statusSelect">
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStatus">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentAppointmentId = null;

        function loadAppointments() {
            $.get('../../actions/get_appointments.php', function(data) {
                const appointments = JSON.parse(data);
                let html = '';
                
                appointments.forEach(app => {
                    const statusClass = {
                        'Pending': 'text-warning',
                        'Confirmed': 'text-primary',
                        'Completed': 'text-success',
                        'Cancelled': 'text-danger'
                    }[app.status] || '';

                    html += `
                        <tr>
                            <td>${app.appointment_date}</td>
                            <td>${app.farmer_fname} ${app.farmer_lname}</td>
                            <td>${app.officer_fname} ${app.officer_lname}</td>
                            <td>${app.description || ''}</td>
                            <td><span class="${statusClass}">${app.status}</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm update-status" data-id="${app.appointment_id}" data-status="${app.status}">
                                    <i class="fas fa-edit"></i> Update
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                $('#appointmentsTable').html(html);
            });
        }

        $(document).ready(function() {
            loadAppointments();

            // Open modal for status update
            $(document).on('click', '.update-status', function() {
                currentAppointmentId = $(this).data('id');
                const currentStatus = $(this).data('status');
                $('#statusSelect').val(currentStatus);
                $('#statusModal').modal('show');
            });

            // Handle status update
            $('#saveStatus').click(function() {
                const status = $('#statusSelect').val();
                
                $.post('../../actions/update_appointment_status.php', {
                    appointment_id: currentAppointmentId,
                    status: status
                }, function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#statusModal').modal('hide');
                        loadAppointments();
                    } else {
                        alert('Failed to update status: ' + result.message);
                    }
                });
            });
        });
    </script>
</body>
</html>
