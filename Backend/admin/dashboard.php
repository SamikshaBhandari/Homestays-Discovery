<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];

// Fetch Counts
$total_homestays = $conn->query("SELECT COUNT(*) as count FROM homestays")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status='pending'")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='user'")->fetch_assoc()['count'];

// --- NEW: FETCH MESSAGE COUNT ---
$total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];

$recent_sql = "SELECT b.booking_id, b.checkin_date, b.status, b.total_price, 
               u.name as user_name, h.name as homestay_name
               FROM bookings b
               JOIN users u ON b.user_id = u.user_id
               JOIN homestays h ON b.homestay_id = h.homestay_id
               ORDER BY b.created_at DESC LIMIT 5";
$recent_bookings = $conn->query($recent_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
    * { 
        margin: 0; 
    padding: 0;
     box-sizing: border-box;
 }
    body { 
        font-family: 'Segoe UI', sans-serif; 
        background: rgb(247, 244, 244); 
    }
    .sidebar {
        width: 250px; 
        background: rgb(44, 62, 80); 
        color: rgb(255, 255, 255);
        padding: 20px; 
        height: 100vh; 
        position: fixed;
    }
    .sidebar h2 { 
        margin-bottom: 30px; 
        color: rgb(52, 152, 219); 
        font-size: 18px; 
    }
    .sidebar a {
        display: block; 
        color: rgb(255, 255, 255); 
        padding: 15px 10px;
        text-decoration: none; 
        margin-bottom: 10px; 
        border-radius: 5px;
        transition: 0.3s; 
        border-left: 4px solid rgba(0, 0, 0, 0);
    }
    .sidebar a:hover, .sidebar a.active { 
        background: rgb(52, 73, 94); 
        border-left-color: rgb(52, 152, 219); 
    }
    .main-content { 
        margin-left: 250px; 
        padding: 30px; 
    }
    .header {
        background: rgb(255, 255, 255); 
        padding: 20px; 
        border-radius: 10px;
        margin-bottom: 30px; 
        display: flex; 
        justify-content: space-between;
        align-items: center; 
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .user-info { 
        display: flex; 
        align-items: center; 
        gap: 15px; 
    }
    .user-info img { 
        width: 40px; 
        height: 40px; 
        border-radius: 50%; 
    }
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px; 
        margin-bottom: 30px;
    }
    .stat-card {
        background: rgb(255, 255, 255); 
        padding: 20px; 
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
        border-top: 4px solid;
    }
    .stat-card.blue {
         border-top-color: rgb(52, 152, 219);
         }
    .stat-card.green {
         border-top-color: rgb(39, 174, 96);
         }
    .stat-card.orange { 
        border-top-color: rgb(243, 156, 18);
     }
    .stat-card.red { 
        border-top-color: rgb(231, 76, 60);
     }
    .stat-card.purple { 
        border-top-color: rgb(155, 89, 182);
     }
    .stat-card h3 { 
        color: rgb(127, 140, 141); 
        font-size: 12px; 
        text-transform: uppercase; 
    }
    .stat-card .number { 
        font-size: 28px; 
        font-weight: bold; 
        color: rgb(44, 62, 80); 
    }
    .recent-bookings {
        background: rgb(255, 255, 255); 
        padding: 25px; 
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 15px; 
    }
    th, td { 
        padding: 12px; 
        text-align: left; 
        border-bottom: 1px solid rgb(236, 240, 241); 
    }
    th { 
        background: rgb(248, 249, 250); 
        color: rgb(127, 140, 141); 
        font-size: 13px; 
    }
    .badge { 
        padding: 4px 10px; 
        border-radius: 12px; 
        font-size: 10px; 
        font-weight: bold; 
    }
    .badge.pending {
        background: rgb(255, 243, 205);
        color: rgb(133, 100, 4);
    }
    .badge.confirmed {
        background: rgb(212, 237, 218); 
        color: rgb(21, 87, 36);
    }
    .view-link { 
        color: rgb(52, 152, 219); 
        text-decoration: none; 
        font-weight: bold; 
    }
</style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa fa-user-shield"></i> Admin Panel</h2>
        <a href="dashboard.php" class="active"><i class="fa fa-chart-pie"></i> Dashboard</a>
        <a href="manage_bookings.php"><i class="fa fa-calendar-check"></i> Manage Bookings</a>
        <a href="manage_homestays.php"><i class="fa fa-home"></i> Manage Homestays</a>
        <a href="manage_users.php"><i class="fa fa-users"></i> Manage Users</a>
        <a href="view_messages.php"><i class="fa fa-envelope"></i> View Messages</a>
        <a href="../../Homestay.php"><i class="fa fa-globe"></i> View Website</a>
        <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1><i class="fa fa-tachometer-alt"></i> Dashboard</h1>
            <div class="user-info">
                <?php 
                    $email = trim($userEmail);
                    $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($email)) . "?d=mp&s=40";
                ?>
                <img src="<?php echo $gravatar_url; ?>" alt="Profile">
                <div>
                    <strong><?php echo htmlspecialchars($userName); ?></strong>
                    <p style="font-size: 12px; color: rgb(127, 140, 141);">Administrator</p>
                </div>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card blue">
                <h3>Total Homestays</h3>
                <div class="number"><?php echo $total_homestays; ?></div>
            </div>
            <div class="stat-card green">
                <h3>Total Bookings</h3>
                <div class="number"><?php echo $total_bookings; ?></div>
            </div>
            <div class="stat-card orange">
                <h3>Pending Bookings</h3>
                <div class="number"><?php echo $pending_bookings; ?></div>
            </div>
            <div class="stat-card red">
                <h3>Total Users</h3>
                <div class="number"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card purple">
                <h3>Total Messages</h3>
                <div class="number"><?php echo $total_messages; ?></div>
                <a href="view_messages.php" style="font-size: 11px; color: rgb(155, 89, 182); text-decoration: none;">View Details →</a>
            </div>
        </div>

        <div class="recent-bookings">
            <h2><i class="fa fa-list"></i> Recent Bookings</h2>
            <?php if ($recent_bookings && $recent_bookings->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Homestay</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $booking['booking_id']; ?></td>
                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['homestay_name']); ?></td>
                        <td>Rs. <?php echo number_format($booking['total_price']); ?></td>
                        <td><span class="badge <?php echo $booking['status']; ?>"><?php echo $booking['status']; ?></span></td>
                        <td><a href="manage_bookings.php" class="view-link">Manage</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; padding: 20px; color: #999;">No recent bookings.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>