<?php
session_start();
require_once 'db_connect.php';

// Fetch machines
$sql = "SELECT * FROM machines";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Machines</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Machines</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Machine Name</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['machine_name']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <a href="update_machine.php?id=<?php echo $row['id']; ?>" class="btn">🔄 Update Status</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
