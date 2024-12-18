<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

try {
    // Fetch extension officers from the database
    $query = "SELECT * FROM extension_officers ORDER BY created_at DESC";
    $result = $db->query($query);

    if (!$result) {
        throw new Exception($db->error);
    }

    $officers = [];
    while ($row = $result->fetch_assoc()) {
        // Remove sensitive information
        unset($row['password_hash']);
        $officers[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $officers]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error fetching officers: ' . $e->getMessage()]);
}
?>