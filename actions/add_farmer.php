<?php
require_once '../db/grinyard_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $first_name = $db->real_escape_string($_POST['first_name']);
    $last_name = $db->real_escape_string($_POST['last_name']);
    $email = $db->real_escape_string($_POST['email']);
    $farm_location = $db->real_escape_string($_POST['farm_location']);
    $farm_type = $db->real_escape_string($_POST['farm_type']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check_query = "SELECT farmer_id FROM farmers WHERE email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
    $check_stmt->close();

    // Insert new farmer
    $query = "INSERT INTO farmers (first_name, last_name, email, farm_location, farm_type, password_hash) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $farm_location, $farm_type, $password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Farmer added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add farmer']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
