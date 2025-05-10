<?php
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP (empty)
$dbname = "gym_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $bmi = $_POST['bmi'];
    $gym_experience = $_POST['gym-experience'];
    $personal_trainer = $_POST['personal-trainer'];
    $membership_type = $_POST['membership-type'];

    $sql = "INSERT INTO users (name, email, password, address, gender, height, weight, bmi, gym_experience, personal_trainer, membership_type)
            VALUES ('$name', '$email', '$password', '$address', '$gender', '$height', '$weight', '$bmi', '$gym_experience', '$personal_trainer', '$membership_type')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Signup Successful!'); window.location.href='login.html';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
