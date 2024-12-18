<?php
require_once '../db/grinyard_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $officer_id = $db->real_escape_string($_POST['officer_id']);
    $first_name = $db->real_escape_string($_POST['first_name']);
    $last_name = $db->real_escape_string($_POST['last_name']);
    $email = $db->real_escape_string($_POST['email']);
    $specialization = $db->real_escape_string($_POST['specialization']);

    // Check if email exists for other officers
    $check_query = "SELECT extension_officer_id FROM extension_officers WHERE email = ? AND extension_officer_id != ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bind_param("si", $email, $officer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
    $check_stmt->close();

    // Update officer details
    $query = "UPDATE extension_officers SET 
              first_name = ?, 
              last_name = ?, 
              email = ?, 
              specialization = ? 
              WHERE extension_officer_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $specialization, $officer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Extension officer updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update extension officer']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
