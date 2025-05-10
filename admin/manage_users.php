<?php
session_start();
require_once 'db_connect.php';

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>

    <!-- Internal CSS Styling -->
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Admin Container */
        .admin-container {
            width: 80%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Header */
        h2 {
            text-align: center;
            color: #333;
        }

        /* Add User Button */
        .add-user {
            text-decoration: none;
            color: white;
            background-color: #28a745;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 15px;
        }

        .add-user:hover {
            background-color: #218838;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Action Links */
        .edit, .delete {
            text-decoration: none;
            padding: 5px 8px;
            border-radius: 4px;
        }

        .edit {
            color: white;
            background-color: #007bff;
        }

        .edit:hover {
            background-color: #0056b3;
        }

        .delete {
            color: white;
            background-color: #dc3545;
        }

        .delete:hover {
            background-color: #c82333;
        }

        /* Back to Dashboard */
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
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
    <div class="admin-container">
        <h2>Manage Users</h2>
        <a href="http://localhost/gym/signup.html" class="add-user">➕ Add New User</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Membership</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['NAME']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['membership_type']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="edit">✏ Edit</a> | 
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure?')">❌ Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
