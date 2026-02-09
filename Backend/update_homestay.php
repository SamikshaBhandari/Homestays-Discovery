<?php
session_start();
include 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $homestay_id = $_POST['homestay_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price = $_POST['price'];

    $sql = "UPDATE homestays SET name = '$name', location = '$location', price = '$price' WHERE homestay_id = '$homestay_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Homestay updated successfully!');
                window.location.href = '../homestaydetail.php?id=$homestay_id';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>