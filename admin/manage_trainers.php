<?php
session_start();
require_once 'db_connect.php';

// Fetch trainers
$sql = "SELECT * FROM trainers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Trainers</title>
    <link rel="stylesheet" href="trainer.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Trainers</h2>
        <a href="http://localhost/gym/trainer/trainer_signup.php" class="btn">➕ Add New Trainer</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Specialization</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['specialization']; ?></td>
                        <td>
                            <a href="edit_trainer.php?id=<?php echo $row['id']; ?>" class="btn">✏ Edit</a> | 
                            <a href="delete_trainer.php?id=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">❌ Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
