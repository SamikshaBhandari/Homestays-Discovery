<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

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
               h.name as homestay_name
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN homestays h ON b.homestay_id = h.homestay_id
        {$filter}
        ORDER BY b.created_at DESC";

$bookings = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings-Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
       * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .sidebar h2 { margin-bottom: 30px; color: rgb(52, 152, 219); font-size: 18px; }
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
        .sidebar a:hover { 
            background: rgb(52, 73, 94); 
            border-left-color: rgb(52, 152, 219); 
        }
        .sidebar a.active { 
            background: rgb(52, 73, 94); 
            border-left-color: rgb(52, 152, 219); 
        }
        .main-content { margin-left: 250px; padding: 30px; }
        .header { 
            background: rgb(255, 255, 255); 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 30px; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
        }
        .header h1 { color: rgb(44, 62, 80); }
        .filters { 
            background: rgb(255, 255, 255); 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            display: flex; 
            gap: 10px; 
            align-items: center; 
        }
        .filters a { 
            padding: 8px 15px; 
            border-radius: 5px; 
            text-decoration: none; 
            background: rgb(236, 240, 241); 
            color: rgb(44, 62, 80); 
        }
        .filters a:hover, .filters a.active { 
            background: rgb(52, 152, 219); 
            color: rgb(255, 255, 255); 
        }
        .booking-table { 
            background: rgb(255, 255, 255); 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
            overflow-x: auto; 
        }
        table { width: 100%; border-collapse: collapse; }
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
        .badge { 
            padding: 5px 10px; 
            border-radius: 15px; 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .badge.pending { background: rgb(255, 243, 205); color: rgb(133, 100, 4); }
        .badge.confirmed { background: rgb(212, 237, 218); color: rgb(21, 87, 36); }
        .badge.cancelled { background: rgb(248, 215, 218); color: rgb(114, 28, 36); }
        
        .action-form { display: inline-flex; gap: 5px; }
        .action-btn { 
            padding: 6px 12px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            color: rgb(255, 255, 255); 
            font-size: 12px; 
            font-weight: 600; 
        }
        .btn-confirm { background: rgb(39, 174, 96); }
        .btn-confirm:hover { background: rgb(34, 153, 84); }
        .btn-cancel { background: rgb(231, 76, 60); }
        .btn-cancel:hover { background: rgb(192, 57, 43); }
        
        .success-msg { 
            background: rgb(212, 237, 218); 
            border-left: 4px solid rgb(40, 167, 69); 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            color: rgb(21, 87, 36); 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa fa-user-shield"></i> Admin Panel</h2>
        <a href="dashboard.php"><i class="fa fa-chart-pie"></i> Dashboard</a>
        <a href="manage_bookings.php" class="active"><i class="fa fa-calendar-check"></i> Manage Bookings</a>
        <a href="manage_homestays.php"><i class="fa fa-home"></i> Manage Homestays</a>
        <a href="manage_users.php"><i class="fa fa-users"></i> Manage Users</a>
        <a href="../../Homestay.php"><i class="fa fa-globe"></i> View Website</a>
        <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1><i class="fa fa-calendar-check"></i> Manage Bookings</h1>
        </div>
        
        <?php if ($message): ?>
        <div class="success-msg">
            <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <div class="filters">
            <strong>Filter by Status:</strong>
            <a href="manage_bookings.php" <?php echo !isset($_GET['status']) ? 'class="active"' : ''; ?>>All</a>
            <a href="manage_bookings.php?status=pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'class="active"' : ''; ?>>Pending</a>
            <a href="manage_bookings.php?status=confirmed" <?php echo isset($_GET['status']) && $_GET['status'] === 'confirmed' ? 'class="active"' : ''; ?>>Confirmed</a>
            <a href="manage_bookings.php?status=cancelled" <?php echo isset($_GET['status']) && $_GET['status'] === 'cancelled' ? 'class="active"' : ''; ?>>Cancelled</a>
        </div>

        <div class="booking-table">
            <?php if ($bookings && $bookings->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Homestay</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Nights</th>
                        <th>Guests</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($booking = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $booking['booking_id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong><br>
                            <small style="color: lightgray;"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($booking['homestay_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['checkin_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['checkout_date'])); ?></td>
                        <td><?php echo $booking['nights']; ?></td>
                        <td><?php echo $booking['guests']; ?></td>
                        <td><strong>Rs. <?php echo number_format($booking['total_price'], 2); ?></strong></td>
                        <td><span class="badge <?php echo $booking['status']; ?>"><?php echo $booking['status']; ?></span></td>
                        <td>
                            <?php if ($booking['status'] === 'pending'): ?>
                            <form method="POST" class="action-form" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <button type="submit" name="status" value="confirmed" class="action-btn btn-confirm"><i class="fa fa-check"></i></button>
                                <button type="submit" name="status" value="cancelled" class="action-btn btn-cancel"><i class="fa fa-times"></i></button>
                            </form>
                            <?php else: ?>
                            <span style="color: lightgray;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: lightgray; padding: 50px;">No bookings found</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>