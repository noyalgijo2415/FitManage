<?php
session_start();
require_once 'db_connect.php';

// Fetch bookings
$sql = "SELECT bookings.id, users.name AS user_name, trainers.name AS trainer_name, bookings.booking_date, bookings.time_slot, bookings.status 
        FROM bookings 
        JOIN users ON bookings.user_id = users.id 
        JOIN trainers ON bookings.trainer_id = trainers.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Bookings</h2>
        <a href="add_booking.php" class="btn">➕ Add Booking</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Member</th><th>Trainer</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td><?php echo $row['trainer_name']; ?></td>
                        <td><?php echo $row['booking_date']; ?></td>
                        <td><?php echo $row['time_slot']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <a href="edit_booking.php?id=<?php echo $row['id']; ?>" class="btn">✏ Edit</a> | 
                            <a href="delete_booking.php?id=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">❌ Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
