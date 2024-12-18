<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'tracey.lartey';
$password = '#Genysys9';
$database = 'webtech_fall2024_tracey_lartey';

// Create connection with error reporting
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Additional connection verification
if (!$conn) {
    error_log("Database connection could not be established");
    die("Database connection failed");
}
?>