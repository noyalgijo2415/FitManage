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

// Check if user is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['client_id'];
$client_name = $_SESSION['user_name'] ?? "Guest";

// Fetch Payment Due Date & Membership
$sql = "SELECT payment_due_date, membership_type FROM users WHERE id='$client_id'";
$result = $conn->query($sql);
$payment_alert = "";
$membership_price = ["basic" => 500, "premium" => 1000, "family" => 2500];

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $payment_due_date = $row['payment_due_date'];
    $membership_type = $row['membership_type'];

    if (!empty($payment_due_date) && strtotime($payment_due_date) < strtotime(date("Y-m-d"))) {
        $payment_alert = "<p class='alert'>⚠️ Your gym fee is overdue! Please make the payment.</p>";
    } else {
        $payment_alert = "<p class='success'>✅ Payment up to date. Next due date: " . ($payment_due_date ?? "Not Set") . "</p>";
    }
}

// Fetch Payment History
$payments = [];
$payment_sql = "SELECT * FROM payments WHERE user_id='$client_id' ORDER BY payment_date DESC";
$payment_result = $conn->query($payment_sql);
if ($payment_result->num_rows > 0) {
    while ($row = $payment_result->fetch_assoc()) {
        $payments[] = $row;
    }
}

// Fetch Trainer Slot Bookings
$bookings = [];
$booking_sql = "SELECT trainer_bookings.id, trainer_bookings.booking_date, trainer_bookings.time_slot, 
                trainers.name AS trainer_name 
                FROM trainer_bookings 
                JOIN trainers ON trainer_bookings.trainer_id = trainers.id 
                WHERE trainer_bookings.user_id='$client_id'";
$booking_result = $conn->query($booking_sql);
if ($booking_result->num_rows > 0) {
    while ($row = $booking_result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Fetch Attendance History
$attendance_records = [];
$attendance_sql = "SELECT scan_time FROM attendance WHERE client_id='$client_id' ORDER BY scan_time DESC";
$attendance_result = $conn->query($attendance_sql);
if ($attendance_result->num_rows > 0) {
    while ($row = $attendance_result->fetch_assoc()) {
        $attendance_records[] = $row['scan_time'];
    }
}

// Handle Payment Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['make_payment'])) {
    $amount = $membership_price[$membership_type] ?? 500;
    $today = date("Y-m-d");

    $payment_insert = "INSERT INTO payments (user_id, amount, payment_date, payment_status) VALUES ('$client_id', '$amount', '$today', 'Completed')";
    if ($conn->query($payment_insert) === TRUE) {
        $new_due_date = date("Y-m-d", strtotime("+1 month", strtotime($today)));
        $conn->query("UPDATE users SET payment_due_date='$new_due_date' WHERE id='$client_id'");

        echo "<script>alert('Payment Successful!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Payment Failed! Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashstyles.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Dashboard Menu</h2>
            <ul>
                <li><a href="booking.php">📅 Book a Trainer Slot</a></li>
               
                <li><a href="machine_availability.php">🏋️ Machine Availability</a></li> <!-- New Link -->
                <div class="prediction-section">
   
    </div>

                <li><a href="profile.php">👤 Profile</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($client_name, ENT_QUOTES, 'UTF-8'); ?>!</h1>

            <!-- Payment Alert -->
            <div class="payment-alert"><?php echo $payment_alert; ?></div>

            <!-- Make Payment -->
            <h2>💳 Make a Payment</h2>
            <form method="POST">
                <p>Membership Plan: <strong><?php echo ucfirst($membership_type); ?></strong></p>
                <p>Amount: <strong>Rs. <?php echo $membership_price[$membership_type] ?? 500; ?></strong></p>
                <button type="submit" name="make_payment">Pay Now</button>
            </form>

            <!-- Payment History -->
            <h2>📜 Your Payment History</h2>
            <?php if (!empty($payments)): ?>
                <table>
                    <thead>
                        <tr><th>Payment ID</th><th>Amount</th><th>Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                <td>Rs. <?php echo htmlspecialchars($payment['amount']); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?><p>No payment records found.</p><?php endif; ?>

            <!-- Trainer Slot Bookings -->
            <h2>📅 Your Trainer Slot Bookings</h2>
            <?php if (!empty($bookings)): ?>
                <table>
                    <thead>
                        <tr><th>Booking ID</th><th>Trainer</th><th>Date</th><th>Time Slot</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['trainer_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?><p>No trainer bookings found.</p><?php endif; ?>

            <!-- QR-Based Attendance -->
            <h2>📌 Your Attendance History</h2>
            <p>Scan your QR code at the gym entrance to mark attendance.</p>
            <a href="generate_qr.php" target="_blank">🔗 Get Your QR Code</a>

            <?php if (!empty($attendance_records)): ?>
                <table>
                    <thead><tr><th>Date & Time</th></tr></thead>
                    <tbody>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr><td><?php echo date("d-M-Y H:i:s", strtotime($record)); ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?><p>No attendance records found.</p><?php endif; ?>
                
        </div>
       

    </div>
    
    


</body>
</html>

<?php $conn->close(); ?>
