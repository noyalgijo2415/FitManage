<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $bmi = $_POST['bmi'];
    $gym_experience = $_POST['gym-experience'];
    $personal_trainer = $_POST['personal-trainer'];
    $membership_type = $_POST['membership-type'];

    // Insert data into database
    $sql = "INSERT INTO users (name, email, password, address, gender, height, weight, bmi, gym_experience, personal_trainer, membership_type)
            VALUES ('$name', '$email', '$password', '$address', '$gender', '$height', '$weight', '$bmi', '$gym_experience', '$personal_trainer', '$membership_type')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href='signup.html';</script>";
    }
}

$conn->close();
?>
