<?php
require_once '../db/grinyard_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = $db->real_escape_string($_POST['farmer_id']);
    $first_name = $db->real_escape_string($_POST['first_name']);
    $last_name = $db->real_escape_string($_POST['last_name']);
    $email = $db->real_escape_string($_POST['email']);
    $farm_location = $db->real_escape_string($_POST['farm_location']);
    $farm_type = $db->real_escape_string($_POST['farm_type']);

    // Check if email exists for other farmers
    $check_query = "SELECT farmer_id FROM farmers WHERE email = ? AND farmer_id != ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bind_param("si", $email, $farmer_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
    $check_stmt->close();

    // Update farmer details
    $query = "UPDATE farmers SET 
              first_name = ?, 
              last_name = ?, 
              email = ?, 
              farm_location = ?, 
              farm_type = ? 
              WHERE farmer_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $farm_location, $farm_type, $farmer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Farmer updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update farmer']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
