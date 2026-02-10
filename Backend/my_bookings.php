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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: rgb(245, 245, 245);
            padding: 40px 20px;
            margin: 0;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgb(255, 255, 255);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .back-nav {
            margin-bottom: 25px;
        }
        .back-link {
            text-decoration: none;
            color: rgb(70, 70, 70);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s ease;
            font-size: 15px;
        }
        .back-link:hover {
            color: rgb(23, 64, 214);
            transform: translateX(-5px);
        }

        h1 {
            color: rgb(33, 37, 41); 
            margin-bottom: 30px;
            font-size: 28px;
            border-bottom: 2px solid rgb(240, 240, 240);
            padding-bottom: 10px;
        }

        .booking-card {
            background: rgb(252, 252, 252); 
            border: 1px solid rgb(230, 230, 230);
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 8px;
            transition: 0.3s;
        }
        .booking-card:hover {
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .booking-card h3 {
            color: rgb(44, 62, 80); 
            margin: 0 0 15px 0;
            font-size: 20px;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .detail {
            padding: 12px;
            background: rgb(255, 255, 255); 
            border-radius: 6px;
            border: 1px solid rgb(240, 240, 240);
            font-size: 14px;
            color: rgb(85, 85, 85);
        }
        .detail strong {
            color: rgb(51, 51, 51);
            margin-right: 5px;
        }

        .status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            text-transform: uppercase;
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
            padding: 10px 22px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .cancel-booking-btn:hover {
            background: rgb(180, 40, 50);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        .no-bookings {
            text-align: center;
            padding: 60px;
            color: rgb(160, 160, 160); 
        }
        .no-bookings i {
            font-size: 50px;
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="back-nav">
            <a href="../Homestay.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Homestays
            </a>
        </div>

        <h1>My Booking History</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while($booking = $result->fetch_assoc()): ?>
                <div class="booking-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <h3><i class="fas fa-home"></i> <?php echo htmlspecialchars($booking['homestay_name']); ?></h3>
                        <span class="status <?php echo $booking['status']; ?>">
                            <?php echo $booking['status']; ?>
                        </span>
                    </div>
                    
                    <div class="booking-details">
                        <div class="detail">
                            <strong><i class="far fa-calendar-alt"></i> Check-in:</strong> <?php echo $booking['checkin_date']; ?>
                        </div>
                        <div class="detail">
                            <strong><i class="fas fa-moon"></i> Nights:</strong> <?php echo $booking['nights']; ?>
                        </div>
                        <div class="detail">
                            <strong><i class="fas fa-users"></i> Guests:</strong> <?php echo $booking['guests']; ?>
                        </div>
                        <div class="detail">
                            <strong><i class="fas fa-tag"></i> Total Price:</strong> Rs. <?php echo number_format($booking['total_price'], 2); ?>
                        </div>
                        <div class="detail">
                            <strong><i class="fas fa-map-marker-alt"></i> Location:</strong> <?php echo htmlspecialchars($booking['location']); ?>
                        </div>
                        <div class="detail">
                            <strong><i class="far fa-clock"></i> Booked on:</strong> <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                        </div>
                    </div>

                    <?php if ($booking['status'] == 'pending'): ?>
                        <div style="text-align: right; border-top: 1px solid rgb(240, 240, 240); padding-top: 15px;">
                            <a href="cancel_booking.php?id=<?php echo $booking['booking_id']; ?>" 
                               class="cancel-booking-btn" 
                               onclick="return confirm('Do you really want to cancel this booking request?')">
                                <i class="fas fa-ban"></i> Cancel Request
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-bookings">
                <i class="fas fa-calendar-times"></i>
                <p>You haven't made any bookings yet.</p>
                <a href="../Homestay.php" style="color: rgb(23, 64, 214);">Browse Homestays</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>