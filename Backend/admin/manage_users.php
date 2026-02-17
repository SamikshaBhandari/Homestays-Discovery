<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];

if (isset($_GET['remove_user'])) {
    $remove_id = (int)$_GET['remove_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'user'");
    $stmt->bind_param("i", $remove_id);
    if ($stmt->execute()) {
        echo "<script>alert('User removed successfully'); window.location.href='manage_users.php';</script>";
    }
    $stmt->close();
}

$users = $conn->query("SELECT * FROM users WHERE role='user' ORDER BY user_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
        }
        .sidebar a { 
            display: block; 
            color: rgba(189, 195, 199, 1); 
            padding: 15px 20px; 
            text-decoration: none; 
            border-bottom: 1px solid rgba(52, 73, 94, 0.5); 
            font-size: 14px;
        }
        .sidebar a:hover, .sidebar a.active { 
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
            width: 40px; height: 40px;
             border-radius: 50%; 
        }

        .card { 
            background-color: rgba(255, 255, 255, 1); 
            border: 1px solid rgba(0, 0, 0, 0.1); 
            padding: 0; 
            overflow: hidden;
        }
        .card-header {
            padding: 15px 20px;
            background-color: rgba(255, 255, 255, 1);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .card-header h3 { 
            margin: 0; 
            font-size: 16px;
             color: rgba(44, 62, 80, 1);
             }
        table {
             width: 100%;
              border-collapse: collapse;
               background: white;
             }
        th, td {
             padding: 12px 20px;
              text-align: left;
               border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                font-size: 13px;
             }
        th { 
            background-color: rgba(248, 249, 250, 1);
             color: rgba(127, 140, 141, 1);
              font-weight: 600;
             }
        
        .user-id {
             color: rgba(52, 152, 219, 1);
              font-weight: bold;
             }
        .btn-remove { 
            color: rgba(231, 76, 60, 1); 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 12px;
            padding: 5px 10px;
            border: 1px solid rgba(231, 76, 60, 0.2);
            border-radius: 4px;
        }
        .btn-remove:hover {
            background-color: rgba(231, 76, 60, 1);
            color: white;
        }
        .badge {
            background-color: rgba(46, 204, 113, 0.1);
            color: rgba(39, 174, 96, 1);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
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
        <a href="manage_users.php" class="active">Manage Users</a>
        <a href="view_messages.php">View Messages</a>
        <a href="../../Homestay.php">Public Site</a>
        <a href="../logout.php" style="color: rgba(231, 76, 60, 1);">Logout</a>
    </div>

    <div class="main">
        
        <div class="top-nav">
            <h3 style="margin:0; color: rgba(44, 62, 80, 1);">Manage Registered Users</h3>
            <div class="admin-profile">
                <div style="text-align: right;">
                    <b style="display:block; font-size:14px;"><?php echo htmlspecialchars($userName); ?></b>
                    <span style="color: rgba(127, 140, 141, 1); font-size: 12px;">Admin</span>
                </div>
                <?php 
                    $email = trim($userEmail);
                    $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($email)) . "?d=mp&s=40";
                ?>
                <img src="<?php echo $gravatar_url; ?>" alt="Admin">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>System Users</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($users->num_rows > 0): ?>
                        <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td class="user-id">#<?php echo $user['user_id']; ?></td>
                            <td><b><?php echo htmlspecialchars($user['name']); ?></b></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? 'Not Provided'); ?></td>
                            <td><span class="badge">USER</span></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></td>
                            <td>
                                <a href="manage_users.php?remove_user=<?php echo $user['user_id']; ?>" 
                                   class="btn-remove" 
                                   onclick="return confirm('Confirm remove this user?')">
                                   Remove
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding:30px; color:gray;">No registered users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
<?php $conn->close(); ?>