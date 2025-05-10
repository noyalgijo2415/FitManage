<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure QR data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['qr_data'])) {
    $qr_data = $_POST['qr_data'];  // Example: "client_id:3"

    // Extract client ID from QR data
    if (preg_match('/client_id:(\d+)/', $qr_data, $matches)) {
        $client_id = $matches[1];

        // Insert attendance record
        $scan_time = date("Y-m-d H:i:s");
        $insert_sql = "INSERT INTO attendance (client_id, scan_time) VALUES ('$client_id', '$scan_time')";

        if ($conn->query($insert_sql) === TRUE) {
            echo "<script>alert('Attendance marked successfully!'); window.location.href='fitmanage.html';</script>";
        } else {
            echo "<script>alert('Error marking attendance.'); window.location.href='fitmanage.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid QR Code.'); window.location.href='fitmanage.html';</script>";
    }
}

$conn->close();
?>
