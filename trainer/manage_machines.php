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

// Fetch trainer's name
$trainer_query = "SELECT name FROM trainers WHERE id='$trainer_id'";
$trainer_result = $conn->query($trainer_query);
$trainer_row = $trainer_result->fetch_assoc();
$trainer_name = $trainer_row['name'];

// Handle machine addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['machine_name'])) {
    $machine_name = $_POST['machine_name'];

    $insert_sql = "INSERT INTO machines (trainer_id, trainer_name, machine_name, status) 
                   VALUES ('$trainer_id', '$trainer_name', '$machine_name', 'available')";
    
    if ($conn->query($insert_sql) === TRUE) {
        header("Location: manage_machines.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle machine status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['machine_id'], $_POST['status'])) {
    $machine_id = $_POST['machine_id'];
    $status = $_POST['status'];

    $update_sql = "UPDATE machines SET status='$status' WHERE id='$machine_id' AND trainer_id='$trainer_id'";
    if ($conn->query($update_sql) === TRUE) {
        header("Location: manage_machines.php");
        exit();
    }
}

// Fetch trainer's machines
$sql = "SELECT * FROM machines WHERE trainer_id='$trainer_id' ORDER BY updated_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Machines - Smart-Fit</title>
    <link rel="stylesheet" href="machine.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>

<div class="dashboard-container">
    <h1>Manage Machine Availability</h1>

    <!-- Add Machine Form -->
    <div class="form-container">
        <h2>Add New Machine</h2>
        <form method="POST">
            <label>Machine Name:</label>
            <input type="text" name="machine_name" required>
            <button type="submit" class="btn-add">Add Machine</button>
        </form>
    </div>

    <!-- Machine List -->
    <table>
        <tr>
            <th>Machine Name</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['machine_name']); ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td><?php echo $row['updated_at']; ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="machine_id" value="<?php echo $row['id']; ?>">
                        <select name="status">
                            <option value="available" <?php echo ($row['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                            <option value="in use" <?php echo ($row['status'] == 'in use') ? 'selected' : ''; ?>>In Use</option>
                            <option value="maintenance" <?php echo ($row['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                        <button type="submit" class="btn-update">Update</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
