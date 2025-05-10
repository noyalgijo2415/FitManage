<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['client_id'];
$bookings_sql = "SELECT tb.booking_date, tb.time_slot, t.name AS trainer_name, t.specialization 
                 FROM trainer_bookings tb 
                 JOIN trainers t ON tb.trainer_id = t.id 
                 WHERE tb.user_id='$user_id'";
$bookings_result = $conn->query($bookings_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="bookingdash.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <div class="dashboard">
        <h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
        <h3>Your Trainer Bookings</h3>
        <table>
            <tr>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Trainer</th>
                <th>Specialization</th>
            </tr>
            <?php while ($row = $bookings_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['booking_date']; ?></td>
                <td><?php echo $row['time_slot']; ?></td>
                <td><?php echo $row['trainer_name']; ?></td>
                <td><?php echo $row['specialization']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
