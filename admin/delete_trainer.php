<?php
require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM trainers WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: manage_trainers.php");
        exit();
    } else {
        echo "Error deleting trainer: " . $conn->error;
    }
}
?>
