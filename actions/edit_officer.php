<?php
session_start();
require_once '../db/config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access";
    header('Location: ../view/LOGIN_Grinyard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $officer_id = $_POST['officer_id'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $specialization = $_POST['specialization'] ?? '';

    // Validate inputs
    $errors = [];
    if (empty($officer_id)) $errors[] = "Officer ID is required";
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($specialization)) $errors[] = "Specialization is required";

    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header('Location: ../view/Admin_dashboard.php');
        exit();
    }

    try {
        // Get current timestamp
        $current_time = date('Y-m-d H:i:s');

        // Prepare SQL statement
        $stmt = $conn->prepare("
            UPDATE extension_officers 
            SET first_name = ?, last_name = ?, email = ?, 
                specialization = ?, updated_at = ?
            WHERE id = ?
        ");

        // Bind parameters
        $stmt->bind_param(
            "sssssi",
            $first_name, $last_name, $email,
            $specialization, $current_time, $officer_id
        );

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['success'] = "Extension officer updated successfully";
        } else {
            if ($conn->errno == 1062) { // Duplicate email error
                $_SESSION['error'] = "Email already exists";
            } else {
                $_SESSION['error'] = "Error updating extension officer: " . $conn->error;
            }
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // Redirect back to dashboard
    header('Location: ../view/Admin_dashboard.php');
    exit();
} else {
    // If not POST request, redirect to dashboard
    header('Location: ../view/Admin_dashboard.php');
    exit();
}
?>
