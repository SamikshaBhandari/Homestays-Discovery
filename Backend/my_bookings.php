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
            background: rgb(245, 245, 245);
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgb(255, 255, 255);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: rgb(51, 51, 51); 
            margin-bottom: 20px;
        }
        .booking-card {
            background: rgb(249, 249, 249); 
            border: 1px solid rgb(221, 221, 221);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
        }
        .booking-card h3 {
            color: rgb(51, 51, 51); 
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
            background: rgb(255, 255, 255); 
            border-radius: 3px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .status.pending {
             background: rgb(255, 243, 205);
              color: rgb(133, 100, 4); 
            }
        .status.confirmed { 
            background: rgb(212, 237, 218);
             color: rgb(21, 87, 36);
             }
        .status.cancelled { 
            background: rgb(248, 215, 218);
             color: rgb(114, 28, 36);
             }
        .cancel-booking-btn {
            background: rgb(220, 53, 69); 
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: 0.3s;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .cancel-booking-btn:hover {
            background: rgb(180, 40, 50);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: rgb(153, 153, 153); 
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

                    <?php if ($booking['status'] == 'pending'): ?>
                        <div style="text-align: right; margin-top: 10px;">
                            <a href="Backend/cancel_booking.php?id=<?php echo $booking['booking_id']; ?>" 
                               class="cancel-booking-btn" 
                               onclick="return confirm('Long time waiting? Do you want to cancel this request and book another homestay?')">
                                <i class="fas fa-ban"></i> Cancel Request
                            </a>
                        </div>
                    <?php endif; ?>
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