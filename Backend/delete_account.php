<?php
session_start();
include 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login.html");
    exit();
}

$userId = $_SESSION['user_id'];

mysqli_query($conn, "DELETE FROM bookings WHERE user_id = '$userId'");

mysqli_query($conn, "DELETE FROM homestay_images WHERE homestay_id IN (SELECT homestay_id FROM homestays WHERE user_id = '$userId')");

mysqli_query($conn, "DELETE FROM homestays WHERE user_id = '$userId'");

$sql = "DELETE FROM users WHERE user_id = '$userId'";

if (mysqli_query($conn, $sql)) {
    session_destroy();
    echo "<script>
            alert('Your account and all related data have been deleted.');
            window.location.href = '../index1.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>