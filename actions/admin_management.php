<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Include your config file
require_once '../db/config.php';

// Enhanced error logging function
function logError($message, $details = []) {
    error_log($message . " " . json_encode($details));
}

// Helper function for sending JSON responses
function respond($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Validate input data
function validateInput($input, $requiredFields, $optionalFields = []) {
    $errors = [];

    // Check required fields
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            $errors[] = "Missing or empty required field: $field";
        }
    }

    // Validate email if present
    if (isset($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    return $errors;
}

// Get HTTP method and endpoint
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

// Handle OPTIONS request for CORS preflight
if ($method == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    switch ($endpoint) {
        case 'farmers':
            switch ($method) {
                case 'GET':
                    $result = $conn->query("SELECT farmer_id as id, CONCAT(first_name, ' ', last_name) as name, email FROM farmers");
                    $farmers = $result->fetch_all(MYSQLI_ASSOC);
                    respond(["success" => true, "data" => $farmers]);
                    break;
                
                case 'POST':
                    // Validate input
                    $validationErrors = validateInput($input, 
                        ['first_name', 'last_name', 'email', 'password'], 
                        ['location', 'farm_type']
                    );
                    
                    if (!empty($validationErrors)) {
                        respond([
                            "success" => false, 
                            "error" => implode(", ", $validationErrors)
                        ], 400);
                    }

                    // Prepare statement
                    $stmt = $conn->prepare("INSERT INTO farmers (first_name, last_name, email, farm_location, farm_type, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
                    
                    // Hash password
                    $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT);
                    
                    // Bind parameters
                    $stmt->bind_param(
                        "ssssss", 
                        $input['first_name'], 
                        $input['last_name'], 
                        $input['email'], 
                        $input['location'] ?? null, 
                        $input['farm_type'] ?? null, 
                        $passwordHash
                    );

                    // Execute and check result
                    if (!$stmt->execute()) {
                        logError("Farmer Insert Error", [
                            'error' => $stmt->error,
                            'input' => $input
                        ]);
                        respond([
                            "success" => false, 
                            "error" => "Database insertion failed: " . $stmt->error
                        ], 500);
                    }

                    respond(["success" => true]);
                    break;
            }
            break;

        case 'officers':
            switch ($method) {
                case 'GET':
                    $result = $conn->query("SELECT extension_officer_id as id, CONCAT(first_name, ' ', last_name) as name, email FROM extension_officers");
                    $officers = $result->fetch_all(MYSQLI_ASSOC);
                    respond(["success" => true, "data" => $officers]);
                    break;
                
                case 'POST':
                    // Validate input
                    $validationErrors = validateInput($input, 
                        ['first_name', 'last_name', 'email', 'password'], 
                        ['specialization']
                    );
                    
                    if (!empty($validationErrors)) {
                        respond([
                            "success" => false, 
                            "error" => implode(", ", $validationErrors)
                        ], 400);
                    }

                    // Prepare statement
                    $stmt = $conn->prepare("INSERT INTO extension_officers (first_name, last_name, email, specialization, password_hash) VALUES (?, ?, ?, ?, ?)");
                    
                    // Hash password
                    $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT);
                    
                    // Bind parameters
                    $stmt->bind_param(
                        "sssss", 
                        $input['first_name'], 
                        $input['last_name'], 
                        $input['email'], 
                        $input['specialization'] ?? null, 
                        $passwordHash
                    );

                    // Execute and check result
                    if (!$stmt->execute()) {
                        logError("Officer Insert Error", [
                            'error' => $stmt->error,
                            'input' => $input
                        ]);
                        respond([
                            "success" => false, 
                            "error" => "Database insertion failed: " . $stmt->error
                        ], 500);
                    }

                    respond(["success" => true]);
                    break;
            }
            break;

        case 'appointments':
            switch ($method) {
                case 'GET':
                    $result = $conn->query("
                        SELECT 
                            appointments.appointment_id as id, 
                            farmers.first_name AS farmer_name, 
                            extension_officers.first_name AS officer_name,
                            appointments.appointment_date as date,
                            appointments.status
                        FROM appointments
                        JOIN farmers ON appointments.farmer_id = farmers.farmer_id
                        JOIN extension_officers ON appointments.officer_id = extension_officers.extension_officer_id
                    ");
                    $appointments = $result->fetch_all(MYSQLI_ASSOC);
                    respond(["success" => true, "data" => $appointments]);
                    break;
                
                case 'POST':
                    // Validate input
                    $validationErrors = validateInput($input, 
                        ['farmer_id', 'officer_id', 'date'], 
                        ['description']
                    );
                    
                    if (!empty($validationErrors)) {
                        respond([
                            "success" => false, 
                            "error" => implode(", ", $validationErrors)
                        ], 400);
                    }

                    // Prepare statement
                    $stmt = $conn->prepare("INSERT INTO appointments (farmer_id, officer_id, appointment_date, description) VALUES (?, ?, ?, ?)");
                    
                    // Bind parameters
                    $stmt->bind_param(
                        "iiss", 
                        $input['farmer_id'], 
                        $input['officer_id'], 
                        $input['date'], 
                        $input['description'] ?? null
                    );

                    // Execute and check result
                    if (!$stmt->execute()) {
                        logError("Appointment Insert Error", [
                            'error' => $stmt->error,
                            'input' => $input
                        ]);
                        respond([
                            "success" => false, 
                            "error" => "Database insertion failed: " . $stmt->error
                        ], 500);
                    }

                    respond(["success" => true]);
                    break;
            }
            break;

        default:
            respond(["success" => false, "error" => "Invalid endpoint"], 404);
    }
} catch (Exception $e) {
    // Catch any unexpected errors
    logError("Unexpected Error", [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    respond([
        "success" => false, 
        "error" => "Unexpected server error: " . $e->getMessage()
    ], 500);
}

// Close the database connection
$conn->close();
?>