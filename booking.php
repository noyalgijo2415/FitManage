<?php
session_start();
require_once 'db_connect.php';
require_once 'send_email.php'; // Include the email function

if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['client_id'];

// Fetch user details
$user_sql = "SELECT name, email FROM users WHERE id='$user_id'";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();
$user_name = $user['name'];
$user_email = $user['email'];

// Fetch trainers from database
$trainers_sql = "SELECT * FROM trainers";
$trainers_result = $conn->query($trainers_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trainer_id = $_POST['trainer_id'];
    $booking_date = $_POST['booking_date'];
    $time_slot = $_POST['time_slot'];

    // Check if the slot is already booked
    $check_sql = "SELECT * FROM trainer_bookings WHERE trainer_id='$trainer_id' AND booking_date='$booking_date' AND time_slot='$time_slot'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows == 0) {
        // Fetch trainer details
        $trainer_sql = "SELECT name FROM trainers WHERE id='$trainer_id'";
        $trainer_result = $conn->query($trainer_sql);
        $trainer = $trainer_result->fetch_assoc();
        $trainer_name = $trainer['name'];

        // Insert booking
        $sql = "INSERT INTO trainer_bookings (user_id, trainer_id, booking_date, time_slot) 
                VALUES ('$user_id', '$trainer_id', '$booking_date', '$time_slot')";
        
        if ($conn->query($sql) === TRUE) {
            // Send email confirmation
            if (sendBookingEmail($user_email, $user_name, $trainer_name, $booking_date, $time_slot)) {
                echo "<script>alert('Booking Confirmed! Email Sent.'); window.location.href='dashboard.php';</script>";
            } else {
                echo "<script>alert('Booking Confirmed, but email failed.'); window.location.href='dashboard.php';</script>";
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "<script>alert('This slot is already booked. Choose another slot.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Trainer Slot</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <div class="booking-container">
        <h2>Book a Trainer Slot</h2>
        <form action="" method="POST">
            <label for="trainer_id">Select Trainer:</label>
            <select name="trainer_id" id="trainer_id" required>
                <?php while ($row = $trainers_result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo $row['name'] . " - " . $row['specialization']; ?>
                    </option>
                <?php } ?>
            </select>

            <label for="booking_date">Select Date:</label>
            <input type="date" id="booking_date" name="booking_date" required>

            <label for="time_slot">Select Time Slot:</label>
            <select id="time_slot" name="time_slot" required>
                <option value="06:00 - 07:00 AM">06:00 - 07:00 AM</option>
                <option value="07:00 - 08:00 AM">07:00 - 08:00 AM</option>
                <option value="08:00 - 09:00 AM">08:00 - 09:00 AM</option>
                <option value="05:00 - 06:00 PM">05:00 - 06:00 PM</option>
                <option value="06:00 - 07:00 PM">06:00 - 07:00 PM</option>
                <option value="07:00 - 08:00 PM">07:00 - 08:00 PM</option>
            </select>

            <button type="submit">Book Slot</button>
        </form>
    </div>
</body>
</html>
