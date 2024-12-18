<?php
// Start session at the beginning
session_start();

// Include database configuration
require_once '../db/config.php';

// Enhanced input sanitization
function sanitizeInput($data) {
    if (!is_string($data)) {
        return $data;
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Comprehensive validation function
function validateRegistrationData($first_name, $last_name, $email, $password, $role) {
    $errors = [];

    // First name validation
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    } elseif (strlen($first_name) < 2 || strlen($first_name) > 50) {
        $errors[] = "First name must be between 2 and 50 characters.";
    }

    // Last name validation
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    } elseif (strlen($last_name) < 2 || strlen($last_name) > 50) {
        $errors[] = "Last name must be between 2 and 50 characters.";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required.";
    } else {
        $passwordErrors = [];
        if (strlen($password) < 8) {
            $passwordErrors[] = "At least 8 characters long";
        }
        if (!preg_match("/[A-Z]/", $password)) {
            $passwordErrors[] = "At least one uppercase letter";
        }
        if (!preg_match("/\d{3}/", $password)) {
            $passwordErrors[] = "At least three digits";
        }
        if (!preg_match("/[!@#$%^&*]/", $password)) {
            $passwordErrors[] = "At least one special character";
        }

        if (!empty($passwordErrors)) {
            $errors[] = "Password requirements not met: " . implode(", ", $passwordErrors);
        }
    }

    // Role validation
    $validRoles = ['farmer', 'extension_officer', 'admin'];
    if (empty($role) || !in_array($role, $validRoles)) {
        $errors[] = "Invalid role selected.";
    }

    return $errors;
}

// Detailed error logging function
function logError($message, $filename = 'registration_errors.log') {
    $logEntry = date('[Y-m-d H:i:s] ') . $message . "\n";
    file_put_contents($filename, $logEntry, FILE_APPEND);
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Print all received POST data
    error_log("Received POST data: " . print_r($_POST, true));

    // Sanitize and capture inputs
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = sanitizeInput($_POST['role'] ?? '');

    // Validate registration data
    $validationErrors = validateRegistrationData($first_name, $last_name, $email, $password, $role);

    if (!empty($validationErrors)) {
        // Log validation errors
        error_log("Validation Errors: " . implode(", ", $validationErrors));
        
        $_SESSION['registration_errors'] = $validationErrors;
        header("Location: ../view/signup.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    try {
        // Prepare SQL based on role
        switch ($role) {
            case 'farmer':
                $farm_location = sanitizeInput($_POST['farm_location'] ?? '');
                $farm_type = sanitizeInput($_POST['farm_type'] ?? '');
                
                $stmt = $conn->prepare("INSERT INTO farmers (first_name, last_name, email, password_hash, farm_location, farm_type) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashed_password, $farm_location, $farm_type);
                break;

            case 'extension_officer':
                $specialization = sanitizeInput($_POST['specialization'] ?? '');
                
                $stmt = $conn->prepare("INSERT INTO extension_officers (first_name, last_name, email, password_hash, specialization) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $specialization);
                break;

            case 'admin':
                $stmt = $conn->prepare("INSERT INTO admins (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
                break;
        }

        // Execute the statement
        if ($stmt->execute()) {
            // Log successful registration
            error_log("Successful registration for: $email (Role: $role)");
            
            $_SESSION['registration_success'] = "Account created successfully! Please log in.";
            header("Location: ../view/LOGIN_Grinyard.php");
            exit();
        } else {
            // Detailed error logging
            $errorDetails = [
                'error_code' => $stmt->errno,
                'error_message' => $stmt->error,
                'sql_state' => $conn->sqlstate
            ];
            
            error_log("Database Insertion Error: " . print_r($errorDetails, true));

            $errors = [];
            if ($conn->errno == 1062) {
                $errors[] = "Email already exists.";
            } else {
                $errors[] = "Database error occurred. Please try again.";
                $errors[] = "Error details have been logged.";
            }

            $_SESSION['registration_errors'] = $errors;
            header("Location: ../view/signup.php");
            exit();
        }
    } catch (Exception $e) {
        // Log the full exception
        error_log("Registration Exception: " . $e->getMessage());
        
        $_SESSION['registration_errors'] = [
            "An unexpected error occurred.",
            "Error: " . $e->getMessage()
        ];
        header("Location: ../view/signup.php");
        exit();
    }
} else {
    // Redirect if accessed directly without POST
    header("Location: ../view/signup.php");
    exit();
}
?>