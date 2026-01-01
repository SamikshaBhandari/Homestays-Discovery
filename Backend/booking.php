<?php
include 'databaseconnection.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Direct access not allowed.");
}

$fname    = $_POST['name'] ?? ''; 
$email    = $_POST['Email'] ?? '';
$phone    = $_POST['phone'] ?? '';
$checkin  = $_POST['checkin'] ?? '';
$nights   = (int)($_POST['nights'] ?? 0);
$guest    = (int)($_POST['guest'] ?? 0);

$price_per_night = 1000;
$total_price = $nights * $price_per_night;

$sql = "INSERT INTO bookings (full_name, email, checkin_date, nights, guests, phone, total_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sssiisd", $fname, $email, $checkin, $nights, $guest, $phone, $total_price);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Successfully your booking is confirmed.');
                window.location.href = '../index1.php'; 
              </script>";
    } else {
        echo "Database Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "SQL Preparation Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>