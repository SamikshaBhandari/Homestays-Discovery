<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    
    if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
        $stmt->bind_param("si", $status, $booking_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['message'] = 'Booking status updated successfully!';
    }
    
    header("Location: manage_bookings.php");
    exit;
}

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$filter = '';
if (isset($_GET['status']) && in_array($_GET['status'], ['pending', 'confirmed', 'cancelled'])) {
    $status_filter = $_GET['status'];
    $filter = " WHERE b.status = '" . $conn->real_escape_string($status_filter) . "'";
}

$sql = "SELECT b.booking_id, b.checkin_date, b.checkout_date, b.nights, b.guests, 
               b.total_price, b.status, b.created_at,
               u.name as user_name, u.email as user_email, u.phone as user_phone,
               h.name as homestay_name,
               owner.name as owner_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN homestays h ON b.homestay_id = h.homestay_id
        LEFT JOIN users owner ON h.user_id = owner.user_id
        {$filter}
        ORDER BY b.created_at DESC";

$bookings = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
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
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
        }
        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filters a {
            padding: 8px 15px;
            text-decoration: none;
            background-color: rgba(255, 255, 255, 1);
            color: rgba(44, 62, 80, 1);
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 13px;
        }
        .filters a.active {
            background-color: rgba(52, 152, 219, 1);
            color: white;
            border-color: rgba(52, 152, 219, 1);
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
            font-size: 13px;
        }
        th {
             background-color: rgba(248, 249, 250, 1); 
             color: rgba(127, 140, 141, 1); 
            }
        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            color: white;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
        }
        .btn-confirm {
             background-color: rgba(39, 174, 96, 1);
             }
        .btn-cancel {
             background-color: rgba(231, 76, 60, 1); 
            }
        .status-badge {
            font-weight: bold;
            font-size: 11px;
            padding: 3px 7px;
            border-radius: 3px;
        }
        .pending {
             color: rgba(230, 126, 34, 1);
              background: rgba(230, 126, 34, 0.1);
             }
        .confirmed {
             color: rgba(39, 174, 96, 1);
              background: rgba(39, 174, 96, 0.1);
             }
        .cancelled {
             color: rgba(231, 76, 60, 1);
              background: rgba(231, 76, 60, 0.1); 
            }
        .success-msg {
            background-color: rgba(212, 237, 218, 1);
            color: rgba(21, 87, 36, 1);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(195, 230, 203, 1);
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_bookings.php" class="active">Manage Bookings</a>
        <a href="manage_homestays.php">Manage Homestays</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="view_messages.php">View Messages</a>
        <a href="../../Homestay.php">Public Site</a>
        <a href="../logout.php" style="color: rgba(231, 76, 60, 1);">Logout</a>
    </div>

    <div class="main">
        
        <div class="top-nav">
            <h3 style="margin:0; color: rgba(44, 62, 80, 1);">Manage All Bookings</h3>
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

        <?php if ($message): ?>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="filters">
            <strong>Filters:</strong>
            <a href="manage_bookings.php" <?php echo !isset($_GET['status']) ? 'class="active"' : ''; ?>>All</a>
            <a href="manage_bookings.php?status=pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'class="active"' : ''; ?>>Pending</a>
            <a href="manage_bookings.php?status=confirmed" <?php echo isset($_GET['status']) && $_GET['status'] === 'confirmed' ? 'class="active"' : ''; ?>>Confirmed</a>
            <a href="manage_bookings.php?status=cancelled" <?php echo isset($_GET['status']) && $_GET['status'] === 'cancelled' ? 'class="active"' : ''; ?>>Cancelled</a>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest</th>
                        <th>Homestay</th>
                        <th>Dates</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings && $bookings->num_rows > 0): ?>
                        <?php while($booking = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $booking['booking_id']; ?></td>
                            <td>
                                <b><?php echo htmlspecialchars($booking['user_name']); ?></b><br>
                                <small><?php echo htmlspecialchars($booking['user_email']); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($booking['homestay_name']); ?><br>
                                <small style="color:gray;"> <?php echo htmlspecialchars($booking['owner_name'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <?php echo date('M d', strtotime($booking['checkin_date'])); ?> - 
                                <?php echo date('M d', strtotime($booking['checkout_date'])); ?>
                            </td>
                            <td>Rs. <?php echo number_format($booking['total_price']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $booking['status']; ?>">
                                    <?php echo strtoupper($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($booking['status'] === 'pending'): ?>
                                <form method="POST" style="display: flex; gap: 5px;" onsubmit="return confirm('Change status?');">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" name="status" value="confirmed" class="btn btn-confirm">Confirm</button>
                                    <button type="submit" name="status" value="cancelled" class="btn btn-cancel">Cancel</button>
                                </form>
                                <?php else: ?>
                                <span style="color: #ccc;">No Action</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
<?php $conn->close(); ?>