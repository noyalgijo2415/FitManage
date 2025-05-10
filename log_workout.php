<?php
session_start();
require_once 'db_connect.php';
require_once 'send_email.php'; // For email notifications

if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['client_id'];
$today = date("Y-m-d");

// Check last workout date
$sql = "SELECT streak_count, last_workout_date, total_workouts FROM user_streaks WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$streak = 1; // Default to 1
$total_workouts = 1; // First-time workout

if ($row) {
    $last_workout = $row['last_workout_date'];
    $streak = $row['streak_count'];
    $total_workouts = $row['total_workouts'];

    if ($last_workout == date("Y-m-d", strtotime("-1 day"))) {
        // Continue streak
        $streak += 1;
    } elseif ($last_workout != $today) {
        // Reset streak if a workout is missed
        $streak = 1;
    }

    $total_workouts += 1;
    $update_sql = "UPDATE user_streaks SET streak_count = '$streak', last_workout_date = '$today', total_workouts = '$total_workouts' WHERE user_id = '$user_id'";
    $conn->query($update_sql);
} else {
    // First-time workout log
    $insert_sql = "INSERT INTO user_streaks (user_id, streak_count, last_workout_date, total_workouts) VALUES ('$user_id', 1, '$today', 1)";
    $conn->query($insert_sql);
}

// Determine if a milestone is reached
$badge = "";
if ($streak == 7) {
    $badge = "🔥 One Week Warrior";
} elseif ($streak == 30) {
    $badge = "🏆 One Month Beast Mode";
} elseif ($total_workouts == 100) {
    $badge = "💪 100 Workouts Completed";
}

// Send email for milestone
if ($badge) {
    sendStreakEmail($_SESSION['user_email'], $_SESSION['user_name'], $streak, $badge);
}

echo "<script>alert('Workout logged! Your streak is now $streak days!'); window.location.href='dashboard.php';</script>";

$conn->close();
?>
