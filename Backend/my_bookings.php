<?php
session_start();
include 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login.html");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$userName = $_SESSION['name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? '';

$sql = "SELECT b.booking_id, b.checkin_date, b.nights, b.guests, 
               b.total_price, b.status, b.created_at, 
               COALESCE(b.checkout_date, DATE_ADD(b.checkin_date, INTERVAL b.nights DAY)) as checkout_date,
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, rgb(102, 126, 234) 0%, rgb(118, 75, 162) 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .header {
            background: rgb(255, 255, 255);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { 
            color: rgb(51, 51, 51);
         }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgb(102, 126, 234);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgb(255, 255, 255);
            font-weight: bold;
        }
        .container { 
        max-width: 1200px;
         margin: 0 auto;
          }
        .booking-card { 
            background: rgb(255, 255, 255); 
            padding: 25px; 
            margin-bottom: 20px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .booking-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgb(240, 240, 240);
        }
        .booking-header h3 { 
            color: rgb(44, 62, 80); 
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status { 
            padding: 8px 20px; 
            border-radius: 25px; 
            font-size: 13px; 
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.pending { 
            background: rgb(255, 243, 205); 
            color: rgb(133, 100, 4); 
            border: 2px solid rgb(255, 193, 7);
        }
        .status.confirmed { 
            background: rgb(212, 237, 218); 
            color: rgb(21, 87, 36); 
            border: 2px solid rgb(40, 167, 69);
        }
        .status.cancelled { 
            background: rgb(248, 215, 218); 
            color: rgb(114, 28, 36); 
            border: 2px solid rgb(220, 53, 69);
        }
        .booking-details { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
        }
        .detail-item { 
            display: flex; 
            align-items: center; 
            gap: 12px;
            padding: 10px;
            background: rgb(248, 249, 250);
            border-radius: 8px;
        }
        .detail-item i { 
            color: rgb(102, 126, 234); 
            font-size: 20px;
            width: 30px;
            text-align: center;
        }
        .detail-item span {
             color: rgb(85, 85, 85);
             }
        .detail-item strong { 
            color: rgb(51, 51, 51);
         }
        
        .back-btn { 
            display: inline-block; 
            margin-bottom: 20px; 
            padding: 12px 25px; 
            background: rgb(255, 255, 255);
            color: rgb(102, 126, 234); 
            text-decoration: none; 
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .back-btn:hover { 
            background: rgb(102, 126, 234);
            color: rgb(255, 255, 255);
            transform: translateX(-5px);
        }
        .no-bookings { 
            text-align: center; 
            padding: 80px 20px; 
            background: rgb(255, 255, 255);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .no-bookings i {
            font-size: 80px;
            color: rgb(221, 221, 221);
            margin-bottom: 20px;
        }
        .no-bookings h2 {
             color: rgb(85, 85, 85); 
             margin-bottom: 10px;
             }
        .no-bookings p {
             color: rgb(136, 136, 136); 
             margin-bottom: 30px; 
            }
        .booking-count {
            background: rgb(102, 126, 234);
            color: rgb(255, 255, 255);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fa fa-calendar-check"></i> My Bookings
                <?php if ($result->num_rows > 0): ?>
                    <span class="booking-count"><?php echo $result->num_rows; ?></span>
                <?php endif; ?>
            </h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                </div>
                <span style="color: gray; font-weight: 500;"><?php echo htmlspecialchars($userName); ?></span>
            </div>
        </div>

        <a href="../Homestay.php" class="back-btn">
            <i class="fa fa-arrow-left"></i> Back to Homestays
        </a>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while($booking = $result->fetch_assoc()): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <h3>
                            <i class="fa fa-home"></i>
                            <?php echo htmlspecialchars($booking['homestay_name']); ?>
                        </h3>
                        <span class="status <?php echo $booking['status']; ?>">
                            <?php echo strtoupper($booking['status']); ?>
                        </span>
                    </div>
                    
                    <div class="booking-details">
                        <div class="detail-item">
                            <i class="fa fa-calendar-check"></i>
                            <span>Check-in: <strong><?php echo date('M d, Y', strtotime($booking['checkin_date'])); ?></strong></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-calendar-times"></i>
                            <span>Check-out: <strong><?php echo date('M d, Y', strtotime($booking['checkout_date'])); ?></strong></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-moon"></i>
                            <span>Nights: <strong><?php echo $booking['nights']; ?> night(s)</strong></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-users"></i>
                            <span>Guests: <strong><?php echo $booking['guests']; ?> guest(s)</strong></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-money-bill-wave"></i>
                            <span>Total: <strong>Rs. <?php echo number_format($booking['total_price'], 2); ?></strong></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($booking['location']); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-clock"></i>
                            <span>Booked on: <strong><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></strong></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-bookings">
                <i class="fa fa-inbox"></i>
                <h2>No bookings yet</h2>
                <p>Start exploring our amazing homestays and make your first booking!</p>
                <a href="../Homestay.php" class="back-btn" style="margin: 0;">
                    <i class="fa fa-search"></i> Browse Homestays
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>