<?php
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    // Redirect to login if not authenticated
    header("Location: ../pages/LOGIN_Grinyard.php");
    exit();
}

// Include database connection
require_once '../db/config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $farmer_id = $_SESSION['user_id'];
    $appointment_id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $extension_officer_id = filter_input(INPUT_POST, 'extension_officer_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, 
        ['options' => ['min_range' => 1, 'max_range' => 5]]);
    $review_text = filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_STRING);

    // Validate inputs
    if (!$appointment_id || !$extension_officer_id || !$rating) {
        $_SESSION['error'] = "Invalid input. Please provide a rating.";
        header("Location: ../view/farmer_dashboard.php");
        exit();
    }

    // Check if the farmer has already rated this appointment
    $checkRatingQuery = "SELECT COUNT(*) as count FROM officer_ratings 
                         WHERE appointment_id = ? AND farmer_id = ?";
    $checkStmt = $conn->prepare($checkRatingQuery);
    $checkStmt->bind_param("ii", $appointment_id, $farmer_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result()->fetch_assoc();

    if ($checkResult['count'] > 0) {
        $_SESSION['error'] = "You have already rated this appointment.";
        header("Location: ../view/farmer_dashboard.php");
        exit();
    }

    // Prepare SQL to insert rating
    $insertQuery = "INSERT INTO officer_ratings (
        appointment_id, 
        farmer_id, 
        extension_officer_id, 
        rating, 
        review_text
    ) VALUES (?, ?, ?, ?, ?)";

    // Prepare and execute statement
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param(
        "iiisi", 
        $appointment_id, 
        $farmer_id, 
        $extension_officer_id, 
        $rating, 
        $review_text
    );

    // Execute the query
    if ($stmt->execute()) {
        $_SESSION['success'] = "Rating submitted successfully!";
    } else {
        $_SESSION['error'] = "Error submitting the rating. Please try again.";
    }

    // Redirect back to the farmer dashboard
    header("Location: ../view/farmer_dashboard.php");
    exit();
}

// Close the connection
$conn->close();
?>
