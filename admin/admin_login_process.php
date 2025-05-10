<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve admin details from database
    $sql = "SELECT * FROM admin WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Directly compare plain text passwords (⚠️ NOT SECURE)
        if ($password === $row['password']) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid Password!'); window.location.href='admin_login.php';</script>";
        }
    } else {
        echo "<script>alert('Admin not found!'); window.location.href='admin_login.php';</script>";
    }
}

$conn->close();
?>
