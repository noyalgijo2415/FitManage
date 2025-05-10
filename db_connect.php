<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>