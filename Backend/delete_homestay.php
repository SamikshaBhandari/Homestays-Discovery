<?php
session_start();
include 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (isset($_GET['id'])) {
    $homestay_id = (int)$_GET['id'];

    $delete_images_sql = "DELETE FROM homestay_images WHERE homestay_id = '$homestay_id'";
    mysqli_query($conn, $delete_images_sql);

    $delete_bookings_sql = "DELETE FROM bookings WHERE homestay_id = '$homestay_id'";
    mysqli_query($conn, $delete_bookings_sql);

    $sql = "DELETE FROM homestays WHERE homestay_id = '$homestay_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Homestay and all related data deleted successfully!');
                window.location.href = '../Homestay.php';
              </script>";
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: ../Homestay.php");
}
?>