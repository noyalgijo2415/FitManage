<?php
session_start();
if (!isset($_SESSION['trainer_id'])) {
    header("Location: trainer_login.php");
    exit();
}

$trainer_name = $_SESSION['trainer_name'];
$specialization = $_SESSION['trainer_specialization'] ?? 'Not Set';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trainer Dashboard - Smart-Fit</title>
    <link rel="icon" type="image/x-icon" href="logo.png">

    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h1 {
            color: #007bff;
            margin-bottom: 10px;
        }

        h3 {
            color: #555;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
        }

        a {
            display: block;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        a:hover {
            background: #0056b3;
        }

        .logout {
            background: #dc3545;
        }

        .logout:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h1>Welcome, <?php echo htmlspecialchars($trainer_name); ?>!</h1>
    <h3>Specialization: <?php echo htmlspecialchars($specialization); ?></h3>

    <ul>
        <li><a href="manage_bookings.php">📅 Manage Bookings</a></li>
        <li><a href="manage_machines.php">🏋️ Update Machine Availability</a></li>
        <li><a href="trainer_logout.php" class="logout">🚪 Logout</a></li>
    </ul>
</div>

</body>
</html>
