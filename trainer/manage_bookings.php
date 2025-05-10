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

// Handle booking status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'], $_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    if ($action == "accept") {
        $update_sql = "UPDATE trainer_bookings SET status='accepted' WHERE id='$booking_id' AND trainer_id='$trainer_id'";
    } elseif ($action == "cancel") {
        $update_sql = "UPDATE trainer_bookings SET status='canceled' WHERE id='$booking_id' AND trainer_id='$trainer_id'";
    }

    if ($conn->query($update_sql) === TRUE) {
        header("Location: manage_bookings.php");
        exit();
    }
}

// Fetch trainer bookings with client details
$sql = "SELECT 
           trainer_bookings.id, 
           trainer_bookings.booking_date, 
            trainer_bookings.time_slot AS booking_time, 
            trainer_bookings.status, 
            COALESCE(users.name, 'Unknown') AS client_name, 
            COALESCE(users.email, 'N/A') AS client_email 
        FROM trainer_bookings 
        LEFT JOIN users ON trainer_bookings.user_id = users.id
        WHERE trainer_bookings.trainer_id = '$trainer_id'
        ORDER BY trainer_bookings.booking_date DESC";

$result = $conn->query($sql);

// Debugging: Check if query returns results
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings - Smart-Fit</title>
    <link rel="stylesheet" href="booking.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>

<div class="dashboard-container">
    <h1>Manage Your Bookings</h1>
    <table>
        <tr>
            <th>Client Name</th>
            <th>Email</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php 
        while ($row = $result->fetch_assoc()) { 
            // Debugging: Print row data to see if it's correctly retrieved
            echo "<pre>"; print_r($row); echo "</pre>";
        ?>
            <tr>
                <td><?php echo isset($row['client_name']) ? htmlspecialchars($row['client_name']) : 'N/A'; ?></td>
                <td><?php echo isset($row['client_email']) ? htmlspecialchars($row['client_email']) : 'N/A'; ?></td>
                <td><?php echo isset($row['booking_date']) ? $row['booking_date'] : 'N/A'; ?></td>
                <td><?php echo isset($row['booking_time']) ? $row['booking_time'] : 'N/A'; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] == 'pending') { ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="accept" class="btn-accept">✔ Accept</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="cancel" class="btn-cancel">❌ Cancel</button>
                        </form>
                    <?php } else {
                        echo "No actions";
                    } ?>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
