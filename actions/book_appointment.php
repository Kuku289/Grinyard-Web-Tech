<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database connection
require_once '../db/config.php';

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    die("Unauthorized access. Please log in.");
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $farmer_id = $_SESSION['user_id'];
    
    // Use the correct column name 'officer_id' from the appointments table
    $officer_id = isset($_POST['extension_officer_id']) ? intval($_POST['extension_officer_id']) : 0;
    $appointment_date = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate inputs
    if (!$officer_id || empty($appointment_date) || empty($description)) {
        die("Error: All fields are required. Please fill in all information.");
        header("Location: ../view/farmer_dashboard.php");
    }

    try {
        // Check for existing appointments
        $checkQuery = "SELECT COUNT(*) as count FROM appointments 
                       WHERE officer_id = ? 
                       AND appointment_date = ? 
                       AND status NOT IN ('Cancelled')";
        
        $checkStmt = $conn->prepare($checkQuery);
        if (!$checkStmt) {
            die("Prepare failed for check query: " . htmlspecialchars($conn->error));
        }
        
        $checkStmt->bind_param("is", $officer_id, $appointment_date);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result()->fetch_assoc();

        if ($checkResult['count'] > 0) {
            die("Error: The selected officer is not available on this date. Please choose another date or officer.");
        }

        // Prepare SQL to insert appointment
        $insertQuery = "INSERT INTO appointments (
            farmer_id, 
            officer_id, 
            appointment_date, 
            description, 
            status
        ) VALUES (?, ?, ?, ?, 'Pending')";

        // Prepare and execute statement
        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            die("Prepare failed for insert query: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param(
            "iiss", 
            $farmer_id, 
            $officer_id, 
            $appointment_date, 
            $description
        );

        // Execute and handle result
        if ($stmt->execute()) {
            // Appointment booked successfully
            $appointment_id = $stmt->insert_id;
            echo json_encode([
                'status' => 'success',
                'message' => 'Appointment booked successfully! Awaiting confirmation from the extension officer.',
                'appointment_id' => $appointment_id
            ]);
            exit();
        } else {
            // Error in booking
            die("Error: Failed to book appointment. " . htmlspecialchars($stmt->error));
        }
    } catch (Exception $e) {
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    // Direct access to the script without POST data
    die("Error: Invalid access method.");
}
?>