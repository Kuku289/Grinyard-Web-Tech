<?php
require_once '../db/grinyard_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = $db->real_escape_string($_POST['farmer_id']);

    // Check if farmer has any appointments
    $check_query = "SELECT appointment_id FROM appointments WHERE farmer_id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bind_param("i", $farmer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete farmer with existing appointments']);
        exit;
    }
    $check_stmt->close();

    // Delete farmer
    $query = "DELETE FROM farmers WHERE farmer_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $farmer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Farmer deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete farmer']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
