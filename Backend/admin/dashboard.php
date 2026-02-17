<?php
session_start();
include '../databaseconnection.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];

$total_homestays = $conn->query("SELECT COUNT(*) as count FROM homestays")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status='pending'")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='user'")->fetch_assoc()['count'];
$total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];

$recent_bookings = $conn->query("SELECT b.booking_id, u.name as user_name, h.name as homestay_name, b.total_price, b.status 
                                 FROM bookings b 
                                 JOIN users u ON b.user_id = u.user_id 
                                 JOIN homestays h ON b.homestay_id = h.homestay_id 
                                 ORDER BY b.created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { 
            margin: 0; 
            font-family: Arial, sans-serif; 
            display: flex; 
            background-color: rgba(244, 247, 246, 1); 
        }
        .sidebar { 
            width: 240px; 
            background-color: rgba(44, 62, 80, 1); 
            color: rgba(255, 255, 255, 1); 
            min-height: 100vh; 
            position: fixed;
        }
        .sidebar h2 { 
            background-color: rgba(26, 37, 47, 1); 
            margin: 0; 
            padding: 20px; 
            font-size: 18px; 
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar a { 
            display: block; 
            color: rgba(189, 195, 199, 1); 
            padding: 15px 20px; 
            text-decoration: none; 
            border-bottom: 1px solid rgba(52, 73, 94, 0.5); 
        }
        .sidebar a:hover { 
            background-color: rgba(52, 73, 94, 1); 
            color: rgba(255, 255, 255, 1); 
        }
        .main { 
            margin-left: 240px; 
            flex: 1; 
            padding: 25px; 
        }
        .top-nav { 
            background-color: rgba(255, 255, 255, 1); 
            padding: 12px 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border: 1px solid rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }
        .admin-profile { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }
        .admin-profile img { 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
        }
        .stats-row { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 25px; 
        }
        .stat-box { 
            background-color: rgba(255, 255, 255, 1); 
            border: 1px solid rgba(0, 0, 0, 0.1); 
            padding: 20px; 
            flex: 1; 
            text-align: center; 
        }
        .stat-box h3 {
             margin: 0;
              font-size: 28px;
               color: rgba(44, 62, 80, 1);
             }
        .stat-box p {
             margin: 5px 0 0;
              font-size: 11px; 
              color: rgba(127, 140, 141, 1);
               font-weight: bold;
             }
        .table-card { 
            background-color: rgba(255, 255, 255, 1); 
            padding: 20px; 
            border: 1px solid rgba(0, 0, 0, 0.1); 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            border-bottom: 1px solid rgba(0, 0, 0, 0.05); 
            padding: 12px; 
            text-align: left; 
            font-size: 14px;
        }
        th { 
            background-color: rgba(248, 249, 250, 1); 
            color: rgba(127, 140, 141, 1); 
        }
                .manage-btn {
            color: rgba(52, 152, 219, 1);
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }
        .manage-btn:hover {
             text-decoration: underline;
             }
        .status-text {
             font-weight: bold;
              font-size: 11px;
             }
        .pending {
             color: rgba(230, 126, 34, 1);
             }
        .confirmed { 
            color: rgba(39, 174, 96, 1);
         }
        
        .logout-btn { 
            color: rgba(231, 76, 60, 1) !important;
             font-weight: bold;
             }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_bookings.php">Manage Bookings</a>
        <a href="manage_homestays.php">Manage Homestays</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="view_messages.php">View Messages</a>
        <a href="../../Homestay.php">Public Site</a>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main">
        
        <div class="top-nav">
            <h3 style="margin:0; color: rgba(44, 62, 80, 1);">Control Overview</h3>
            <div class="admin-profile">
                <div style="text-align: right;">
                    <b style="display:block; font-size:14px;"><?php echo htmlspecialchars($userName); ?></b>
                    <span style="color: rgba(127, 140, 141, 1); font-size: 12px;">Admin</span>
                </div>
                <?php 
                    $email = trim($userEmail);
                    $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($email)) . "?d=mp&s=40";
                ?>
                <img src="<?php echo $gravatar_url; ?>" alt="Profile">
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <h3><?php echo $total_homestays; ?></h3>
                <p>HOMESTAYS</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $total_bookings; ?></h3>
                <p>BOOKINGS</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $pending_bookings; ?></h3>
                <p>PENDING</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $total_users; ?></h3>
                <p>TOTAL USERS</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $total_messages; ?></h3>
                <p>MESSAGES</p>
            </div>
        </div>

        <div class="table-card">
            <h4 style="margin-top:0; color: rgba(44, 62, 80, 1);">Recent Activity Log</h4>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Homestay</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th> </tr>
                </thead>
                <tbody>
                    <?php if ($recent_bookings->num_rows > 0): ?>
                        <?php while($row = $recent_bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['booking_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['homestay_name']); ?></td>
                            <td>Rs. <?php echo number_format($row['total_price']); ?></td>
                            <td class="status-text <?php echo strtolower($row['status']); ?>">
                                <?php echo strtoupper($row['status']); ?>
                            </td>
                            <td>
                                <a href="manage_bookings.php" class="manage-btn">Manage</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No recent records.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
<?php $conn->close(); ?>