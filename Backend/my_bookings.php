<?php
session_start();
include 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login.html");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT b.booking_id, b.checkin_date, b.nights, b.guests, 
               b.total_price, b.status, b.created_at,
               h.name as homestay_name, h.location
        FROM bookings b
        JOIN homestays h ON b.homestay_id = h.homestay_id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="../css/index.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .booking-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .booking-card h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .booking-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        .detail {
            padding: 10px;
            background: white;
            border-radius: 3px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        .status.pending {
            background: #fff3cd;
            color: #856404;
        }
        .status.confirmed {
            background: #d4edda;
            color: #155724;
        }
        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Bookings</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while($booking = $result->fetch_assoc()): ?>
                <div class="booking-card">
                    <h3><?php echo htmlspecialchars($booking['homestay_name']); ?></h3>
                    <span class="status <?php echo $booking['status']; ?>">
                        <?php echo strtoupper($booking['status']); ?>
                    </span>
                    
                    <div class="booking-details">
                        <div class="detail">
                            <strong>Check-in:</strong> <?php echo $booking['checkin_date']; ?>
                        </div>
                        <div class="detail">
                            <strong>Nights:</strong> <?php echo $booking['nights']; ?>
                        </div>
                        <div class="detail">
                            <strong>Guests:</strong> <?php echo $booking['guests']; ?>
                        </div>
                        <div class="detail">
                            <strong>Total Price:</strong> Rs. <?php echo number_format($booking['total_price'], 2); ?>
                        </div>
                        <div class="detail">
                            <strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?>
                        </div>
                        <div class="detail">
                            <strong>Booked on:</strong> <?php echo $booking['created_at']; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-bookings">
                <p>No bookings found. Start booking now!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>