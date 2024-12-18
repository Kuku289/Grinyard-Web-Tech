<?php
require_once '../db/grinyard_db.php';

$query = "SELECT a.*, 
          f.first_name as farmer_fname, f.last_name as farmer_lname,
          e.first_name as officer_fname, e.last_name as officer_lname
          FROM appointments a
          LEFT JOIN farmers f ON a.farmer_id = f.farmer_id
          LEFT JOIN extension_officers e ON a.officer_id = e.extension_officer_id
          ORDER BY a.appointment_date DESC";

$result = mysqli_query($conn, $query);
$appointments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

echo json_encode($appointments);
?>
