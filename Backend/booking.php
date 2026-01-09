<?php
session_start();
include './databaseconnection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login to book!'); window.location.href='../Login.html';</script>";
        exit;
    }

    $user_id = (int)$_SESSION['user_id'];
    $checkin_date = $_POST['checkin'];
    $nights = (int)$_POST['nights'];
    $guests = (int)$_POST['guest'];
    
    $homestay_id = (int)$_POST['homestay_id']; 

    $price_query = "SELECT price FROM homestays WHERE homestay_id = ?";
    $stmt_price = $conn->prepare($price_query);
    $stmt_price->bind_param("i", $homestay_id);
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();
    
    if ($result_price->num_rows > 0) {
        $row = $result_price->fetch_assoc();
        $price_per_night = $row['price'];
    } else {
        echo "<script>alert('Homestay not found!'); window.history.back();</script>";
        exit;
    }
    $total_price = $nights * $price_per_night;
    $status = 'pending'; 

    $sql = "INSERT INTO bookings (user_id, homestay_id, checkin_date, nights, guests, total_price, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iisiids", $user_id, $homestay_id, $checkin_date, $nights, $guests, $total_price, $status);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Booking Request Sent! Total Price: Rs. $total_price');
                    window.location.href = '../confirm_booking.php'; 
                  </script>";
        } else {
            echo "<script>alert('Booking failed. Please try again.'); window.history.back();</script>";
        }
        $stmt->close();
    }
    $stmt_price->close();
}
$conn->close();
?>