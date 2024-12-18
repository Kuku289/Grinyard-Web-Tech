<?php
require_once __DIR__ . '/../db/grinyard_db.php';

function getFarmerById($id) {
    global $db;
    $query = "SELECT * FROM farmers WHERE farmer_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getOfficerById($id) {
    global $db;
    $query = "SELECT * FROM extension_officers WHERE extension_officer_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAppointmentById($id) {
    global $db;
    $query = "SELECT a.*, 
              f.first_name as farmer_fname, f.last_name as farmer_lname,
              e.first_name as officer_fname, e.last_name as officer_lname
              FROM appointments a
              LEFT JOIN farmers f ON a.farmer_id = f.farmer_id
              LEFT JOIN extension_officers e ON a.officer_id = e.extension_officer_id
              WHERE a.appointment_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getFarmerAppointments($farmer_id) {
    global $db;
    $query = "SELECT a.*, e.first_name as officer_fname, e.last_name as officer_lname
              FROM appointments a
              LEFT JOIN extension_officers e ON a.officer_id = e.extension_officer_id
              WHERE a.farmer_id = ?
              ORDER BY a.appointment_date DESC";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $farmer_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getOfficerAppointments($officer_id) {
    global $db;
    $query = "SELECT a.*, f.first_name as farmer_fname, f.last_name as farmer_lname
              FROM appointments a
              LEFT JOIN farmers f ON a.farmer_id = f.farmer_id
              WHERE a.officer_id = ?
              ORDER BY a.appointment_date DESC";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $officer_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getAppointmentStats() {
    global $db;
    $stats = [];
    
    // Total appointments
    $query = "SELECT COUNT(*) as total FROM appointments";
    $result = $db->query($query);
    $stats['total'] = $result->fetch_assoc()['total'];
    
    // Appointments by status
    $query = "SELECT status, COUNT(*) as count FROM appointments GROUP BY status";
    $result = $db->query($query);
    $stats['by_status'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_status'][$row['status']] = $row['count'];
    }
    
    return $stats;
}
?>
