<?php
session_start();
include 'Backend/databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.html');
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];
$userId = $_SESSION['user_id'];

if (isset($_GET['mark_read'])) {
    $notif_id = (int)$_GET['mark_read'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $userId);
    $stmt->execute();
    $stmt->close();
    header('Location: Notifications.php');
    exit;
}

if (isset($_GET['mark_all_read'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
    header('Location: Notifications.php');
    exit;
}

$unread_notifications = 0;
$notif_query = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = $userId AND is_read = 0");
if ($notif_query) {
    $notif_count = $notif_query->fetch_assoc();
    $unread_notifications = $notif_count['count'];
}

$notifications_sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($notifications_sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$notifications = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifications</title>
    <link rel="stylesheet" href="./css/index.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
      body { 
        background-color: rgb(248, 249, 250);
     }
      .notifications-page {
         padding: 100px 20px 60px;
          max-width: 900px;
           margin: 0 auto;
         }
      .page-header { 
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
      }
      .page-header h1 { 
        color: rgb(44, 62, 80); 
        font-size: 2.5rem;
         margin: 0;
         }
      .mark-all-btn {
        background: rgb(52, 152, 219);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
      }
      .mark-all-btn:hover {
         background: rgb(41, 128, 185); 
        }
      .notification-card { 
        background: white; 
        padding: 20px; 
        margin-bottom: 15px; 
        border-radius: 8px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid rgb(52, 152, 219);
        transition: all 0.3s;
      }
      .notification-card.unread { 
        background: rgb(248, 251, 255);
        border-left-color: rgb(0, 123, 255);
      }
      .notification-card:hover {
         box-shadow: 0 4px 12px rgba(0,0,0,0.15);
         }
      .notification-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: start; 
        margin-bottom: 10px; 
      }
      .notification-title { 
        color: rgb(44, 62, 80); 
        font-weight: 600; 
        margin: 0;
        font-size: 16px;
      }
      .notification-time { 
        color: rgb(127, 140, 141); 
        font-size: 12px; 
        margin-top: 5px;
      }
      .notification-type {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
      }
      .type-booking {
         background: rgb(212, 237, 218);
          color: rgb(21, 87, 36);
         }
      .type-review {
         background: rgb(255, 243, 205);
          color: rgb(133, 100, 4);
         }
      .type-message {
         background: rgb(209, 236, 241);
          color: rgb(23, 162, 184);
         }
      .notification-message { 
        color: rgb(85, 85, 85); 
        margin: 10px 0;
        line-height: 1.6;
      }
      .notification-action { 
        margin-top: 15px; 
        display: flex; 
        gap: 10px; 
      }
      .action-btn { 
        background: rgb(52, 152, 219); 
        color: white; 
        padding: 8px 16px; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        text-decoration: none;
        font-weight: 600;
        font-size: 12px;
        display: inline-block;
      }
      .action-btn:hover { 
        background: rgb(41, 128, 185); 
    }
      .action-btn.secondary {
         background: rgb(189, 195, 199);
         }
      .action-btn.secondary:hover {
         background: rgb(149, 165, 166);
         }
      .no-notifications { 
        text-align: center; 
        padding: 60px 20px; 
        color: rgb(127, 140, 141); 
      }
      .no-notifications i { 
        font-size: 64px; 
        color: rgb(189, 195, 199); 
        margin-bottom: 20px;
        display: block;
      }
      .unread-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: rgb(0, 123, 255);
        border-radius: 50%;
        margin-right: 8px;
      }
    </style>
</head>
<body>
    <header>
        <div class="image">
            <img src="images/logo.png" alt="Logo" />
        </div>
        <div class="navigation">
            <a href="index1.php">Home</a>
            <a href="Homestay.php">Homestays</a>
            <?php if ($isLoggedIn): ?>
                <a href="Backend/my_bookings.php">My Bookings</a>
                <a href="Notifications.php" style="position: relative;">
                    Notifications
                    <?php if ($unread_notifications > 0): ?>
                        <span style="position: absolute; top: -8px; right: -10px; background: rgb(220, 53, 69); color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                            <?php echo $unread_notifications; ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <a href="Contact.php">Contact</a>
        </div>
        <div class="Login_container">
            <?php if ($isLoggedIn): 
                $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($userEmail)) . "?d=mp&s=40";
            ?>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="Backend/profile.php">
                        <div style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; border: 2px solid gray;">
                            <img src="<?php echo $gravatar_url; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </a>
                    <span style="color: gray; font-weight: bold;">
                        <?php echo htmlspecialchars($userName); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="notifications-page">
        <div class="page-header">
            <h1><i class="fa fa-bell"></i> Notifications (<?php echo mysqli_num_rows($notifications); ?>)</h1>
            <?php if (mysqli_num_rows($notifications) > 0 && $unread_notifications > 0): ?>
                <a href="Notifications.php?mark_all_read=1" class="mark-all-btn">
                    <i class="fa fa-check-double"></i> Mark All as Read
                </a>
            <?php endif; ?>
        </div>

        <?php if ($notifications && mysqli_num_rows($notifications) > 0): ?>
            <?php while ($notif = mysqli_fetch_assoc($notifications)): 
                $is_unread = $notif['is_read'] == 0 ? 'unread' : '';
                $type_class = 'type-' . $notif['type'];
            ?>
            <div class="notification-card <?php echo $is_unread; ?>">
                <div class="notification-header">
                    <div style="flex: 1;">
                        <h3 class="notification-title">
                            <?php if ($notif['is_read'] == 0): ?>
                                <span class="unread-dot"></span>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($notif['title']); ?>
                        </h3>
                        <div class="notification-time">
                            <i class="fa fa-clock"></i> 
                            <?php echo date('M d, Y - h:i A', strtotime($notif['created_at'])); ?>
                        </div>
                    </div>
                    <span class="notification-type <?php echo $type_class; ?>">
                        <?php echo ucfirst($notif['type']); ?>
                    </span>
                </div>
                
                <p class="notification-message"><?php echo htmlspecialchars($notif['message']); ?></p>
                
                <div class="notification-action">
                    <?php if ($notif['type'] === 'booking' && !empty($notif['booking_id'])): ?>
                        <a href="Backend/my_bookings.php" class="action-btn">
                            <i class="fa fa-calendar-check"></i> View Booking
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($notif['is_read'] == 0): ?>
                        <a href="Notifications.php?mark_read=<?php echo $notif['notification_id']; ?>" class="action-btn secondary">
                            <i class="fa fa-check"></i> Mark as Read
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-notifications">
                <i class="fa fa-bell-slash"></i>
                <p style="font-size: 18px; margin-bottom: 10px;">You don't have any notifications yet.</p>
                <p>Your booking updates will appear here.</p>
                <a href="Homestay.php">
                    <button style="background: rgb(52, 152, 219); color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 20px;">
                        <i class="fa fa-home"></i> Browse Homestays
                    </button>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="main_section">
          <div class="media">
            <img src="Images/logo.png" />
            <p>
            Discover authentic Nepali hospitality through our carefully selected homestays.<br />
            Experience local culture, breathtaking landscapes, and unforgettable adventures.
          </p>
            <div class="icons">
              <button
                ><a class="fa-brands fa-facebook" style="color: blue"></a
              ></button>
              <button
                ><a class="fa-brands fa-instagram" style="color: red"></a
              ></button>
            </div>
          </div>
          <div class="link">
            <h2>Quick Links</h2>
            <div class="tags">
              <a href="Homestay.php">Homestays</a>
              <a href="Contact.php">Contact Us</a>
              <a href="index1.php">Home</a>
            </div>
          </div>
          <div class="contact">
          <h2>Contact</h2>
          <div class="number">
            <p><i class="fa-solid fa-location-dot"></i>Sunsari ,Nepal</p>
            <p><i class="fa-solid fa-phone"></i>9742869769</p>
            <p><i class="fa-solid fa-envelope"></i>Travellocal2@gmail.com</p>
          </div>
        </div>
      </div>
      </div>
       <div class="copyright">
       <p> <i class="fa-regular fa-copyright"></i> 2025 TravelLocal Nepal. All
        rights reserved.</p>
       </div>
    </footer>
</body>
</html>
<?php mysqli_close($conn); ?>