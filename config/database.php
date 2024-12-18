<?php
function getConnection() {
    $host = 'localhost';
    $dbname = 'grinyard_db';
    $username = 'root';
    $password = '';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        throw new PDOException("Database connection failed");
    }
}
?>
