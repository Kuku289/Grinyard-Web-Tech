<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $appointment_id = $db->real_escape_string($_POST['appointment_id']);
    $status = $db->real_escape_string($_POST['status']);

    // Validate status
    $valid_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception('Invalid status value');
    }

    // Update the appointment status
    $query = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("si", $status, $appointment_id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Appointment status updated successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error updating appointment status: ' . $e->getMessage()]);
}
?>