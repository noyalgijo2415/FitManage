<?php
require_once 'db_connect.php';

// Check if 'id' exists in URL to prevent errors
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid trainer ID.");
}

$id = intval($_GET['id']); // Sanitize ID

// Prepare statement to fetch trainer data
$stmt = $conn->prepare("SELECT * FROM trainers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Trainer not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];

    // Prepare statement for update
    $update_stmt = $conn->prepare("UPDATE trainers SET name=?, email=?, specialization=? WHERE id=?");
    $update_stmt->bind_param("sssi", $name, $email, $specialization, $id);

    if ($update_stmt->execute()) {
        header("Location: manage_trainers.php");
        exit();
    } else {
        echo "Error updating trainer: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Trainer</title>

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

        .edit-container {
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

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
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
    <div class="edit-container">
        <h2>Edit Trainer</h2>
        <form method="POST">
            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
            <input type="text" name="specialization" value="<?php echo htmlspecialchars($row['specialization']); ?>" required>
            <button type="submit">Update Trainer</button>
        </form>
        <a href="manage_trainers.php" class="back-btn">⬅️ Back</a>
    </div>
</body>
</html>
