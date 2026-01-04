<?php
include 'databaseconnection.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login first!'); window.location.href='../Login.html';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $region = mysqli_real_escape_string($conn, $_POST['region']);
    $price = $_POST['price'];
    $rooms = $_POST['total_rooms'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $upload_dir = "../images/"; 
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            echo "<script>alert('Photo upload failed!'); window.history.back();</script>";
            exit;
        }
    }
    $query = "INSERT INTO homestays (name, location, region, description, price, total_rooms, user_id, profile_image) 
              VALUES ('$name', '$location', '$region', '$description', '$price', '$rooms', '$user_id', '$image_name')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Success! Your homestay is listed.');
                window.location.href='../Homestay.php';
              </script>";
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
}
?>