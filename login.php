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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        session_start();
        $_SESSION['client_id'] = $row['id'];
        $_SESSION['user_name'] = $row['NAME']; // Ensure this is set correctly
        $_SESSION['user_email'] = $row['email'];

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Incorrect Email or Password!'); window.location.href='login.html';</script>";
    }
}

$conn->close();
?>
