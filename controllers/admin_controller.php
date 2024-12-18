<?php
session_start();
require_once '../db/config.php';

// Check if user is logged in as admin
function checkAdminAuth() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

// Get database connection
$conn = $GLOBALS['conn'];

// Handle different actions
$action = $_GET['action'] ?? '';

switch($action) {
    case 'getFarmers':
        checkAdminAuth();
        getFarmers($conn);
        break;
    case 'getOfficers':
        checkAdminAuth();
        getOfficers($conn);
        break;
    case 'getAppointments':
        checkAdminAuth();
        getAppointments($conn);
        break;
    case 'addFarmer':
        checkAdminAuth();
        addFarmer($conn);
        break;
    case 'deleteFarmer':
        checkAdminAuth();
        deleteFarmer($conn);
        break;
    case 'logout':
        logout();
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Functions to handle different actions
function getFarmers($conn) {
    try {
        $query = "SELECT farmer_id as id, CONCAT(first_name, ' ', last_name) as name, email FROM farmers";
        $result = $conn->query($query);
        
        $farmers = [];
        while ($row = $result->fetch_assoc()) {
            $farmers[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $farmers]);
    } catch(Exception $e) {
        error_log("Database error in getFarmers: " . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function getOfficers($conn) {
    try {
        $query = "SELECT extension_officer_id as id, CONCAT(first_name, ' ', last_name) as name, email FROM extension_officers";
        $result = $conn->query($query);
        
        $officers = [];
        while ($row = $result->fetch_assoc()) {
            $officers[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $officers]);
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function getAppointments($conn) {
    try {
        $query = "
            SELECT 
                a.appointment_id as id,
                CONCAT(f.first_name, ' ', f.last_name) as farmer_name,
                CONCAT(o.first_name, ' ', o.last_name) as officer_name,
                a.appointment_date as date,
                a.status
            FROM appointments a
            JOIN farmers f ON a.farmer_id = f.farmer_id
            JOIN extension_officers o ON a.extension_officer_id = o.extension_officer_id
        ";
        $result = $conn->query($query);
        
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $appointments]);
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function addFarmer($conn) {
    try {
        // Get form data
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $farm_location = $_POST['farm_location'] ?? '';
        $farm_type = $_POST['farm_type'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Get current timestamp
        $current_time = date('Y-m-d H:i:s');
        
        // Prepare SQL statement
        $stmt = $conn->prepare("
            INSERT INTO farmers (
                first_name, last_name, email, farm_location, farm_type, 
                password_hash, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Bind parameters
        $stmt->bind_param(
            "ssssssss",
            $first_name, $last_name, $email, $farm_location, $farm_type,
            $password_hash, $current_time, $current_time
        );
        
        // Execute the statement
        $result = $stmt->execute();
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            // Check for duplicate email
            if ($conn->errno == 1062) {
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add farmer: ' . $conn->error]);
            }
        }
    } catch(Exception $e) {
        error_log("Error adding farmer: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteFarmer($conn) {
    try {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $conn->prepare("DELETE FROM farmers WHERE farmer_id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete farmer']);
        }
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function logout() {
    session_destroy();
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
}
?>
