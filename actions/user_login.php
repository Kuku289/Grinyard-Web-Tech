<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../db/config.php';

// Reset any previous errors
$_SESSION['login_errors'] = [];

// Input validation function
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to log login attempts
// function logLoginAttempt($email, $role, $success) {
//     $logFile = '../logs/login_attempts.log';
//     $logEntry = date('Y-m-d H:i:s') . " | Email: $email | Role: $role | Status: " . 
//                 ($success ? 'SUCCESS' : 'FAILED') . " | IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
//     file_put_contents($logFile, $logEntry, FILE_APPEND);
// }

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = sanitizeInput($_POST['role']);

    // Validation
    $errors = [];
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($role)) {
        $errors[] = "Role is required";
    }

    // If there are validation errors
    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        header("Location: ../view/Grinyard_index.php");
        exit();
    }

    try {
        // Determine table based on role
        $tableMap = [
            'farmer' => ['table' => 'farmers', 'id_column' => 'farmer_id'],
            'extension_officer' => ['table' => 'extension_officers', 'id_column' => 'extension_officer_id'],
            'admin' => ['table' => 'admins', 'id_column' => 'admin_id']
        ];

        if (!isset($tableMap[$role])) {
            throw new Exception("Invalid role selected");
        }

        $table = $tableMap[$role]['table'];
        $idColumn = $tableMap[$role]['id_column'];

        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT $idColumn, first_name, last_name, email, password_hash FROM $table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Store user details in session
                $_SESSION['user_id'] = $user[$idColumn];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $role;
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];

                // Log successful login
                // logLoginAttempt($email, $role, true);

                // var_dump($role);
                // exit();

                // Redirect based on role
                switch ($role) {
                    case 'farmer':
                        header("Location: ../view/farmer_dashboard.php");
                        break;
                    case 'extension_officer':
                        header("Location: ../view/extension_officer dashboard.php");
                        break;
                    case 'admin':
                        header("Location: ../view/admin/dashboard.php");
                        break;
                }
                exit();
            } else {
                // Log failed login attempt
                // logLoginAttempt($email, $role, false);
                
                $_SESSION['login_errors'] = ["Invalid email or password"];
                header("Location: ../../index.php");
                exit();
            }
        } else {
            // Log failed login attempt
            // logLoginAttempt($email, $role, false);
            
            $_SESSION['login_errors'] = ["User not found"];
            header("Location: ../../Grinyard_index.php");
            exit();
        }
    } catch (Exception $e) {
        // Log unexpected errors
        error_log("Login Error: " . $e->getMessage());
        
        $_SESSION['login_errors'] = ["An unexpected error occurred. Please try again."];
        header("Location: ../../Grinyard_index.php");
        exit();
    }
} else {
    // If accessed directly without POST
    header("Location: ../../Grinyard_index.php");
    exit();
}
?>