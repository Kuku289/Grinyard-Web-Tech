<?php
require_once '../db/grinyard_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $first_name = $db->real_escape_string($_POST['first_name']);
    $last_name = $db->real_escape_string($_POST['last_name']);
    $email = $db->real_escape_string($_POST['email']);
    $specialization = $db->real_escape_string($_POST['specialization']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check_query = "SELECT extension_officer_id FROM extension_officers WHERE email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
    $check_stmt->close();

    // Insert new officer
    $query = "INSERT INTO extension_officers (first_name, last_name, email, specialization, password_hash) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $specialization, $password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Extension officer added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add extension officer']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
