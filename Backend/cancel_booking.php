<?php
session_start();
include 'databaseconnection.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../Login.html");
    exit;
}

$booking_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

$sql = "UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND user_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);

if ($stmt->execute()) {
    echo "<script>
            alert('Booking request cancelled successfully.');
            window.location.href = 'my_bookings.php'; 
          </script>";
} else {
    echo "<script>
            alert('Error: Could not cancel booking.');
            window.location.href = 'my_bookings.php';
          </script>";
}

$stmt->close();
$conn->close();
?>