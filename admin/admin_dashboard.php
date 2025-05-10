<?php
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total counts
$total_members = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_trainers = $conn->query("SELECT COUNT(*) AS total FROM trainers")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];
$total_payments = $conn->query("SELECT SUM(amount) AS total FROM payments WHERE payment_status = 'Completed'")->fetch_assoc()['total'];
$total_machines = $conn->query("SELECT COUNT(*) AS total FROM machines")->fetch_assoc()['total'];
$total_attendance = $conn->query("SELECT COUNT(*) AS total FROM attendance")->fetch_assoc()['total'];

// Fetch payment data for graph
$payment_data = [];
$payment_dates = [];

$payment_query = $conn->query("SELECT DATE(payment_date) as date, SUM(amount) as total FROM payments WHERE payment_status = 'Completed' GROUP BY DATE(payment_date) ORDER BY payment_date ASC");

while ($row = $payment_query->fetch_assoc()) {
    $payment_dates[] = $row['date'];
    $payment_data[] = $row['total'];
}

// Fetch check-in data for graph
$checkin_data = [];
$checkin_dates = [];

$checkin_query = $conn->query("SELECT DATE(scan_time) as date, COUNT(*) as total FROM attendance GROUP BY DATE(scan_time) ORDER BY scan_time ASC");

while ($row = $checkin_query->fetch_assoc()) {
    $checkin_dates[] = $row['date'];
    $checkin_data[] = $row['total'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Gym Management</title>
    <link rel="icon" type="image/x-icon" href="logo.png">
    <link rel="stylesheet" href="dashadmin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js Library -->
    <style>
    .chart-container {
        width: 50%; /* Reduce width */
        max-width: 600px; /* Limit maximum size */
        margin: 20px auto; /* Center it */
    }

    canvas {
        width: 100% !important;
        height: 300px !important; /* Set a smaller height */
    }
</style>


</head>
<body>

<div class="admin-container">
    <nav class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="manage_users.php">👤 Manage Users</a></li>
            <li><a href="manage_trainers.php">🏋️ Manage Trainers</a></li>
            <li><a href="http://localhost/gym/fitmanage.html">🚪 Logout</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <h1>Welcome, Admin!</h1>

        <div class="dashboard-overview">
            <div class="card">
                <h3>👤 Total Members</h3>
                <p><?php echo $total_members; ?></p>
            </div>
            <div class="card">
                <h3>🏋️ Total Trainers</h3>
                <p><?php echo $total_trainers; ?></p>
            </div>
            <div class="card">
                <h3>📅 Total Bookings</h3>
                <p><?php echo $total_bookings; ?></p>
            </div>
            <div class="card">
                <h3>💳 Total Payments (Rs)</h3>
                <p><?php echo $total_payments; ?></p>
            </div>
            <div class="card">
                <h3>🏋️‍♂️ Machines Available</h3>
                <p><?php echo $total_machines; ?></p>
            </div>
            <div class="card">
                <h3>📌 Attendance Records</h3>
                <p><?php echo $total_attendance; ?></p>
            </div>
        </div>

        <!-- Payment Line Graph -->
        <div class="chart-container">
            <h2>📊 Payment Updates Over Time</h2>
            <canvas id="paymentChart"></canvas>
        </div>

        <!-- Check-in Line Graph -->
        <div class="chart-container">
            <h2>📊 Daily Check-ins</h2>
            <canvas id="checkinChart"></canvas>
        </div>

    </div>
</div>

<script>
    // Payment Data from PHP
    const paymentDates = <?php echo json_encode($payment_dates); ?>;
    const paymentAmounts = <?php echo json_encode($payment_data); ?>;

    // Check-in Data from PHP
    const checkinDates = <?php echo json_encode($checkin_dates); ?>;
    const checkinCounts = <?php echo json_encode($checkin_data); ?>;

    // Chart.js for Payment Graph
    const ctx1 = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: paymentDates,
            datasets: [{
                label: 'Total Payments (Rs)',
                data: paymentAmounts,
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Payment Amount (Rs)' } }
            }
        }
    });

    // Chart.js for Check-in Graph
    const ctx2 = document.getElementById('checkinChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: checkinDates,
            datasets: [{
                label: 'Number of Check-ins',
                data: checkinCounts,
                borderColor: 'green',
                backgroundColor: 'rgba(0, 255, 0, 0.2)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Check-ins' } }
            }
        }
    });
</script>

</body>
</html>
