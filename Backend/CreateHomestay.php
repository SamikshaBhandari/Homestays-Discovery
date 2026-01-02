<?php
include 'databaseconnection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access!");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    $region = mysqli_real_escape_string($conn, $_POST['region']);
    $price = $_POST['price'];
    $rooms = $_POST['total_rooms'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);


    $image_names = []; 
    $upload_dir = "../images/";
    $temp_name = $_FILES['image']['tmp_name'][0];
    
  if (!empty($_FILES['image']['name'][0])) {
        foreach ($_FILES['image']['name'] as $key => $val) {
            $file_name = time() . "_" . basename($_FILES['image']['name'][$key]);
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'][$key], $target_path)) {
                $image_names[] = $file_name;
            }
        }
    }
    if (move_uploaded_file($temp_name, $upload_path)) {
        $query = "INSERT INTO homestays (name, location, region, description, price, total_rooms, user_id, image) 
                  VALUES ('$name', '$location', '$region', '$description', '$price', '$rooms', '$user_id', '$image_name')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Homestay added successfully!'); window.location.href='../Homestay.php';</script>";
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Image could not be uploaded.";
    }
}
?>