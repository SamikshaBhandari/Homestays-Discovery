<?php
session_start();
include './databaseconnection.php'; 

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to book!'); window.location.href='../Login.html';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_SESSION['user_id'];
    $homestay_id = (int)($_POST['homestay_id'] ?? 0);
    $checkin_date = trim($_POST['checkIn'] ?? '');
    $checkout_date = trim($_POST['checkout'] ?? '');
    $guests = (int)($_POST['guest'] ?? 1);
    
    if (!$homestay_id || !$checkin_date || !$checkout_date) {
        echo "<script>alert('Please fill all required fields!'); window.history.back();</script>";
        exit;
    }
    $checkin = new DateTime($checkin_date);
    $checkout = new DateTime($checkout_date);
    $nights = $checkout->diff($checkin)->days;
    
    if ($nights <= 0) {
        echo "<script>alert('Check-out date must be after check-in date!'); window.history.back();</script>";
        exit;
    }
    $price_query = "SELECT price, name FROM homestays WHERE homestay_id = ?";
    $stmt_price = $conn->prepare($price_query);
    $stmt_price->bind_param("i", $homestay_id);
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();
    
    if ($result_price->num_rows === 0) {
        echo "<script>alert('Homestay not found!'); window.history.back();</script>";
        $stmt_price->close();
        exit;
    }
    
    $homestay = $result_price->fetch_assoc();
    $price_per_night = (float)$homestay['price'];
    $homestay_name = $homestay['name'];
    $stmt_price->close();
    
    $total_price = $nights * $price_per_night;
    $status = 'pending';

    $sql = "INSERT INTO bookings (user_id, homestay_id, checkin_date, checkout_date, nights, guests, total_price, status,created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iissiids", $user_id, $homestay_id, $checkin_date, $checkout_date, $nights, $guests, $total_price, $status);

        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            
            echo "<script>
                    alert('Booking Request Sent Successfully!\\n\\nHomestay: $homestay_name\\nNights: $nights\\nGuests: $guests\\nTotal: Rs. " . number_format($total_price, 2) . "');
                    window.location.href = 'my_bookings.php';
                  </script>";
        } else {
            echo "<script>alert('Booking failed: " . $conn->error . "'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error!'); window.history.back();</script>";
    }
}

$conn->close();
?>