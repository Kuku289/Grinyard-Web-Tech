<?php
require_once '../db/grinyard_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $officer_id = $db->real_escape_string($_POST['officer_id']);

    // Check if officer has any appointments
    $check_query = "SELECT appointment_id FROM appointments WHERE officer_id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bind_param("i", $officer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete officer with existing appointments']);
        exit;
    }
    $check_stmt->close();

    // Delete officer
    $query = "DELETE FROM extension_officers WHERE extension_officer_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $officer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Extension officer deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete extension officer']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
