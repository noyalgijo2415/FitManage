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

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$whereClause = "";

if ($filter == "paid") {
    $whereClause = "AND status = 'Paid'";
} elseif ($filter == "pending") {
    $whereClause = "AND status = 'Pending'";
}

$sql = "SELECT * FROM payments WHERE user_id='$user_id' $whereClause ORDER BY due_date DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <div class="sidebar">
        <h2>Gym Dashboard</h2>
        <a href="dashboard.php">🏠 Home</a>
        <a href="payment_history.php">💰 Payment History</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="content">
        <h2>💳 Payment History</h2>
        <label>Filter:</label>
        <select onchange="filterPayments(this.value)">
            <option value="all" <?= $filter == "all" ? "selected" : "" ?>>All</option>
            <option value="paid" <?= $filter == "paid" ? "selected" : "" ?>>Paid</option>
            <option value="pending" <?= $filter == "pending" ? "selected" : "" ?>>Pending</option>
        </select>

        <table>
            <tr>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Payment Date</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td>Rs. <?php echo $row['amount']; ?></td>
                <td><?php echo $row['due_date']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['payment_date'] ? $row['payment_date'] : 'N/A'; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <script>
        function filterPayments(value) {
            window.location.href = "payment_history.php?filter=" + value;
        }
    </script>
</body>
</html>
