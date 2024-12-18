<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

try {
    // Fetch farmers from the database
    $query = "SELECT * FROM farmers ORDER BY created_at DESC";
    $result = $db->query($query);

    if (!$result) {
        throw new Exception($db->error);
    }

    $farmers = [];
    while ($row = $result->fetch_assoc()) {
        // Remove sensitive information
        unset($row['password_hash']);
        $farmers[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $farmers]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error fetching farmers: ' . $e->getMessage()]);
}
?>