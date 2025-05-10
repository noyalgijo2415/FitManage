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

// Check if user is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['client_id'];

// Fetch user profile data from the database
$sql = "SELECT NAME,email, address, gender, height, weight, bmi, gym_experience, personal_trainer, membership_type, created_at, payment_due_date FROM users WHERE id='$client_id'";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user profile
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $gym_experience = $_POST['gym_experience'];
    $personal_trainer = $_POST['personal_trainer'];
    $membership_type = $_POST['membership_type'];

    $update_sql = "UPDATE users SET NAME='$name', email='$email', address='$address', gender='$gender', height='$height', weight='$weight', gym_experience='$gym_experience', personal_trainer='$personal_trainer', membership_type='$membership_type' WHERE id='$client_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "<p class='success'>Profile updated successfully!</p>";
        // Refresh user data
        $user_data['NAME'] = $name;
        $user_data['email'] = $email;
        $user_data['address'] = $address;
        $user_data['gender'] = $gender;
        $user_data['height'] = $height;
        $user_data['weight'] = $weight;
        $user_data['gym_experience'] = $gym_experience;
        $user_data['personal_trainer'] = $personal_trainer;
        $user_data['membership_type'] = $membership_type;
    } else {
        echo "<p class='error'>Error updating profile: " . $conn->error . "</p>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <div class="profile-container">
        <h1>User Profile</h1>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['NAME'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            
           
            <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($user_data['address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male" <?php echo ($user_data['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($user_data['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="other" <?php echo ($user_data['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="height">Height (cm):</label>
            <input type="number" id="height" name="height" value="<?php echo htmlspecialchars($user_data['height'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" name="weight" value="<?php echo htmlspecialchars($user_data['weight'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="form-group">
                <label for="gym_experience">Gym Experience:</label>
                <select id="gym_experience" name="gym_experience" required>
                    <option value="yes" <?php echo ($user_data['gym_experience'] == 'yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="no" <?php echo ($user_data['gym_experience'] == 'no') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="personal_trainer">Personal Trainer:</label>
                <select id="personal_trainer" name="personal_trainer" required>
                    <option value="yes" <?php echo ($user_data['personal_trainer'] == 'yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="no" <?php echo ($user_data['personal_trainer'] == 'no') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="membership_type">Membership Type:</label>
                <select id="membership_type" name="membership_type" required>
                    <option value="basic" <?php echo ($user_data['membership_type'] == 'basic') ? 'selected' : ''; ?>>Basic</option>
                    <option value="premium" <?php echo ($user_data['membership_type'] == 'premium') ? 'selected' : ''; ?>>Premium</option>
                    <option value="family" <?php echo ($user_data['membership_type'] == 'family') ? 'selected' : ''; ?>>Family</option>
                </select>
            </div>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
      

           

           