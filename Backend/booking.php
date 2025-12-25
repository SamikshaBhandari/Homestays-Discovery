<?php
include 'databaseconnection.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Direct access not allowed.");
}
$fname    = mysqli_real_escape_string($conn, $_POST['Fname'] ?? ''); 
$email    = mysqli_real_escape_string($conn, $_POST['Email'] ?? '');
$phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$checkin  = mysqli_real_escape_string($conn, $_POST['checkin'] ?? '');
$nights   = (int)($_POST['nights'] ?? 0);
$guest    = (int)($_POST['guest'] ?? 0);

$price_per_night = 1000;
$total_price = $nights * $price_per_night;

$sql = "INSERT INTO bookings (full_name, email, checkin_date, nights, guests, phone, total_price) 
        VALUES ('$fname', '$email', '$checkin', $nights, $guest, '$phone', $total_price)";

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Successfully your booking is confirmed.');
            window.location.href = '../index1.html'; 
          </script>";
} else {
    echo "Database Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>