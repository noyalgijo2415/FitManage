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

// Fetch machine availability
$machines = [];
$sql = "SELECT machine_name, status FROM machines ORDER BY machine_name ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $machines[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Machine Availability</title>
    <link rel="stylesheet" href="dashstyles.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <div class="dashboard-container">
        <h1>🏋️ Machine Availability</h1>
        <table>
            <thead>
                <tr><th>Machine Name</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($machines)): ?>
                    <?php foreach ($machines as $machine): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($machine['machine_name']); ?></td>
                            <td class="<?php echo strtolower($machine['status']); ?>">
                                <?php echo ucfirst($machine['status']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2">No machines found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
