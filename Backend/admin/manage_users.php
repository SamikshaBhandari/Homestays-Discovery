<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

// --- Naya Thapeko Delete User Logic ---
if (isset($_GET['remove_user'])) {
    $remove_id = (int)$_GET['remove_user'];
    // Admin lai delete garna namilne security
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { 
            margin: 0; 
            padding: 0;
             box-sizing: border-box;
             }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: rgb(245, 245, 245); 
        }
        .container { display: flex; }
        .sidebar {
            width: 250px;
            background: rgb(44, 62, 80);
            color: rgb(255, 255, 255);
            position: fixed;
            height: 100vh;
            padding: 20px;
            overflow-y: auto;
        }
        .sidebar h2 { 
            margin-bottom: 30px; 
            color: rgb(52, 152, 219); 
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
            flex: 1;
        }
        .header { 
            background: rgb(255, 255, 255);
            padding: 20px;
            border-radius: 10px; 
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header h1 { 
            color: rgb(44, 62, 80);
         }
        .user-table { 
            background: rgb(255, 255, 255);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto; 
        }
        table {
             width: 100%;
              border-collapse: collapse;
             }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgb(236, 240, 241);
        }
        th { 
            background: rgb(248, 249, 250); 
            font-weight: 600; 
            color: rgb(127, 140, 141);
            font-size: 13px;
        }
        tr:hover {
             background-color: rgb(252, 252, 252);
             }
        .user-id { 
            color: rgb(52, 152, 219);
             font-weight: 600;
             }
        .btn-remove {
            color: rgb(220, 53, 69);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa fa-user-shield"></i> Admin Panel</h2>
        <a href="dashboard.php"><i class="fa fa-chart-pie"></i> Dashboard</a>
        <a href="manage_bookings.php"><i class="fa fa-calendar-check"></i> Manage Bookings</a>
        <a href="manage_homestays.php"><i class="fa fa-home"></i> Manage Homestays</a>
        <a href="manage_users.php" class="active"><i class="fa fa-users"></i> Manage Users</a>
        <a href="view_messages.php"><i class="fa fa-envelope"></i> View Messages</a>
        <a href="../../Homestay.php"><i class="fa fa-globe"></i> View Website</a>
        <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1><i class="fa fa-users"></i> Manage Users</h1>
        </div>

        <div class="user-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td class="user-id">#<?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></td>
                        <td>
                            <a href="manage_users.php?remove_user=<?php echo $user['user_id']; ?>" 
                               class="btn-remove" 
                               onclick="return confirm('Are you sure you want to remove this user?')">
                               <i class="fa fa-user-times"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>