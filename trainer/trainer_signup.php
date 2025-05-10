<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

// Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $specialization = trim($_POST["specialization"]);

    // Check if email already exists
    $check_email = "SELECT * FROM trainers WHERE email='$email'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        $message = "⚠️ Email already registered!";
    } else {
        // Insert new trainer
        $insert_sql = "INSERT INTO trainers (name, email, password, specialization) VALUES ('$name', '$email', '$password', '$specialization')";
        if ($conn->query($insert_sql) === TRUE) {
            header("Location: trainer_login.php");
            exit();
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Signup - Smart-Fit</title>
    <link rel="icon" type="image/x-icon" href="logo.png">
    <style>
        /* Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        h2 {
            margin-bottom: 10px;
            color: #333;
        }

        p {
            font-size: 14px;
            color: red;
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            background: #6366F1;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #4F46E5;
        }

        a {
            color: #6366F1;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Trainer Signup</h2>
    <p><?php echo $message; ?></p>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="specialization" placeholder="Specialization (e.g., Strength Training, Yoga)" required>
        <button type="submit">Signup</button>
    </form>
    <p>Already have an account? <a href="trainer_login.php">Login here</a></p>
</div>

</body>
</html>
