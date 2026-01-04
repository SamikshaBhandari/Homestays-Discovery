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
    $homestay_id = 1; 

    $price_per_night = 1000;
    $total_price = $nights * $price_per_night;
    $status = 'pending'; // Status lai variable ma rakheko

    // Query ma columns ko order milayeko
    $sql = "INSERT INTO bookings (user_id, homestay_id, checkin_date, nights, guests, total_price, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        /* Data Types: 
           i = integer (user_id, homestay_id, nights, guests)
           s = string (checkin_date, status)
           d = double/decimal (total_price)
           Kram: i, i, s, i, i, d, s (Jamma 7 wata)
        */
        $stmt->bind_param("iisiids", $user_id, $homestay_id, $checkin_date, $nights, $guests, $total_price, $status);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Booking Confirmed! Total Price: Rs. $total_price');
                    window.location.href = '../index1.php';
                  </script>";
        } else {
            echo "Execution Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Query Prepare Error: " . $conn->error;
    }
}
$conn->close();
?>