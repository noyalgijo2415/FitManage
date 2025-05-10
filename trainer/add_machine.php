<?php
session_start();
if (!isset($_SESSION['trainer_id'])) {
    header("Location: trainer_login.php");
    exit();
}

$trainer_id = $_SESSION['trainer_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add a new machine
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['machine_name'])) {
    $machine_name = $_POST['machine_name'];

    $trainer_query = "SELECT name FROM trainers WHERE id='$trainer_id'";
    $trainer_result = $conn->query($trainer_query);
    $trainer_row = $trainer_result->fetch_assoc();
    $trainer_name = $trainer_row['name'];

    $insert_sql = "INSERT INTO machines (trainer_id, trainer_name, machine_name, status) VALUES ('$trainer_id', '$trainer_name', '$machine_name', 'available')";
    
    if ($conn->query($insert_sql) === TRUE) {
        header("Location: manage_machines.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Machine - Smart-Fit</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>

<div class="form-container">
    <h2>Add New Machine</h2>
    <form method="POST">
        <label>Machine Name:</label>
        <input type="text" name="machine_name" required>
        <button type="submit" class="btn-add">Add Machine</button>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
