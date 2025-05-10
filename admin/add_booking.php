<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $trainer_id = $_POST['trainer_id'];
    $booking_date = $_POST['booking_date'];
    $time_slot = $_POST['time_slot'];

    $sql = "INSERT INTO bookings (user_id, trainer_id, booking_date, time_slot, status) VALUES ('$user_id', '$trainer_id', '$booking_date', '$time_slot', 'pending')";

    if ($conn->query($sql) === TRUE) {
        header("Location: manage_bookings.php");
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
    <title>Add Booking</title>
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <h2>Add New Booking</h2>
    <form method="POST">
        Member ID: <input type="text" name="user_id" required><br>
        Trainer ID: <input type="text" name="trainer_id" required><br>
        Date: <input type="date" name="booking_date" required><br>
        Time Slot: <input type="text" name="time_slot" required><br>
        <button type="submit">Add Booking</button>
    </form>
    <a href="manage_bookings.php">⬅️ Back</a>
</body>
</html>
