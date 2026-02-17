<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];

$messages_sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$messages_result = mysqli_query($conn, $messages_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { 
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
}
body { 
    font-family: sans-serif; 
    background: rgba(240, 242, 245, 1); 
    display: flex; 
}
.sidebar { 
    width: 240px; 
    background: rgba(44, 62, 80, 1); 
    min-height: 100vh; 
    position: fixed; 
}
.sidebar h2 { 
    color: rgba(255, 255, 255, 1); 
    padding: 20px; 
    text-align: center; 
    background: rgba(30, 39, 46, 1); 
    font-size: 18px; 
}
.sidebar a { 
    display: block; 
    color: rgba(189, 195, 199, 1); 
    padding: 15px 20px; 
    text-decoration: none; 
    border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
}
.sidebar a:hover, .sidebar a.active { 
    background: rgba(52, 152, 219, 1); 
    color: rgba(255, 255, 255, 1); 
}
.main { 
    margin-left: 240px; 
    width: 100%; 
    padding: 20px; 
}
.top-nav { 
    background: rgba(255, 255, 255, 1); 
    padding: 10px 20px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    border-radius: 5px; 
    margin-bottom: 20px; 
}
.admin-info { 
    display: flex; 
    align-items: center; 
    gap: 10px; 
}
.admin-info img { 
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
}
.msg-box { 
    background: rgba(255, 255, 255, 1); 
    padding: 20px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    display: flex; 
    gap: 15px; 
    border: 1px solid rgba(0,0,0,0.05); 
}
.user-pic { 
    width: 50px; 
    height: 50px; 
    border-radius: 50%; 
}
.msg-body { 
    flex: 1; 
}
.msg-header { 
    display: flex; 
    justify-content: space-between; 
    margin-bottom: 5px; 
}
.msg-name { 
    font-weight: bold; 
    color: rgb(19, 20, 21); 
}
.msg-date { 
    font-size: 12px; 
    color: rgba(127, 140, 141, 1); 
}
.msg-meta { 
    font-size: 13px; 
    color: rgb(73, 73, 74); 
    margin-bottom: 10px; 
}
.msg-text { 
    background: rgba(248, 249, 250, 1); 
    padding: 10px; 
    border-radius: 5px; 
    font-size: 14px; 
    color: rgb(59, 60, 60); 
    line-height: 1.5; 
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
        <a href="view_messages.php" class="active">View Messages</a>
        <a href="../../Homestay.php">Public Site</a>
        <a href="../logout.php" style="color: rgba(231, 76, 60, 1);">Logout</a>
    </div>

    <div class="main">
        
        <div class="top-nav">
            <h3 style="color: rgba(44, 62, 80, 1);">User Feedbacks</h3>
            <div class="admin-info">
                <div style="text-align: right;">
                    <b style="font-size: 14px; display: block;"><?php echo $userName; ?></b>
                    <span style="color: rgba(127, 140, 141, 1); font-size: 12px;">Admin</span>
                </div>
                <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($userEmail))); ?>?d=mp" alt="Admin">
            </div>
        </div>

        <?php while ($row = mysqli_fetch_assoc($messages_result)): ?>
            <div class="msg-box">
                <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($row['email']))); ?>?d=mp" class="user-pic">
                
                <div class="msg-body">
                    <div class="msg-header">
                        <span class="msg-name"><?php echo htmlspecialchars($row['full_name']); ?></span>
                        <span class="msg-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                    </div>
                    
                    <div class="msg-meta">
                        <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?> | 
                        <i class="fa fa-phone"></i> <?php echo htmlspecialchars($row['phone']); ?> |
                        <b>Subject:</b> <?php echo htmlspecialchars($row['subject']); ?>
                    </div>

                    <div class="msg-text">
                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>

</body>
</html>
<?php mysqli_close($conn); ?>