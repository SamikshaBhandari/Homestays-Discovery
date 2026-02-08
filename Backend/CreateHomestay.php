<?php
include 'databaseconnection.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['user_id'])) {
        die("User not logged in");
    }

    $user_id = $_SESSION['user_id'];

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $region = mysqli_real_escape_string($conn, $_POST['region']);
    $price = $_POST['price'];
    $rooms = $_POST['total_rooms'];
    $host_name = $_POST['host_name'];
    $rating = $_POST['rating'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $upload_dir = "../images/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $primary_image = "";
    $uploaded_images = [];

    $file_count = isset($_FILES['images']) ? count($_FILES['images']['name']) : 0;

    if ($file_count > 0) {

        foreach ($_FILES['images']['name'] as $key => $original_name) {

            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $new_name = time() . "_" . uniqid() . "_" . basename($original_name);
            $target_path = $upload_dir . $new_name;

            if (move_uploaded_file($tmp_name, $target_path)) {

                if ($key === 0) {
                    $primary_image = $new_name;
                }

                $uploaded_images[] = $new_name;
                echo "Uploaded: $new_name<br>";

            } else {
                echo "Failed to upload: $original_name<br>";
            }
        }
    }

    $query = "
        INSERT INTO homestays
        (name, location, region, description, price, total_rooms, user_id, profile_image, host_name, rating)
        VALUES
        ('$name', '$location', '$region', '$description', '$price', '$rooms', '$user_id', '$primary_image', '$host_name', '$rating')
    ";

    if (!mysqli_query($conn, $query)) {
        die("Database error: " . mysqli_error($conn));
    }

    $homestay_id = mysqli_insert_id($conn);
    if (!empty($uploaded_images)) {
        foreach ($uploaded_images as $img) {
            mysqli_query(
                $conn,
                "INSERT INTO homestay_images (homestay_id, image)
                 VALUES ('$homestay_id', '$img')"
            );
        }
    } else {
        echo "No images to insert<br>";
    }

    echo "<script> alert('Success! Your homestay is listed.'); window.location.href='../Homestay.php'; </script>";
}
?>
