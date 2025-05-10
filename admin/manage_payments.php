<?php
session_start();
require_once 'db_connect.php';

// Fetch payments
$sql = "SELECT payments.id, users.name AS user_name, payments.amount, payments.payment_date, payments.payment_status 
        FROM payments 
        JOIN users ON payments.user_id = users.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Member</th><th>Amount (Rs)</th><th>Date</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td>Rs. <?php echo $row['amount']; ?></td>
                        <td><?php echo $row['payment_date']; ?></td>
                        <td><?php echo ucfirst($row['payment_status']); ?></td>
                        <td>
                            <a href="update_payment.php?id=<?php echo $row['id']; ?>" class="btn">✔ Mark Paid</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
