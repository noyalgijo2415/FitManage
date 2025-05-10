<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $membership = $_POST['membership_type'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (name, email, membership_type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $membership);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
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

        .add-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .back-btn {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            background: #6c757d;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="add-container">
        <h2>Add New User</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <select name="membership_type">
                <option value="basic">Basic</option>
                <option value="premium">Premium</option>
                <option value="family">Family</option>
            </select>
            <button type="submit">Add User</button>
        </form>
        <a href="manage_users.php" class="back-btn">⬅️ Back</a>
    </div>
</body>
</html>
