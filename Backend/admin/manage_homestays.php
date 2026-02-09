<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM homestays WHERE homestay_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Homestay deleted successfully'); window.location.href='manage_homestays.php';</script>";
    }
    $stmt->close();
}

$homestays = $conn->query("SELECT * FROM homestays ORDER BY homestay_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Homestays</title>
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
        .sidebar a:hover, 
        .sidebar a.active { 
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
        .homestay-table { 
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
        img { 
            width: 50px; 
            height: 50px; 
            border-radius: 5px; 
            object-fit: cover;
            border: 1px solid rgb(236, 240, 241);
        }
        .price-tag {
             color: rgb(39, 174, 96);
         font-weight: bold;
         }
        .btn-delete {
            color: rgb(220, 53, 69);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-delete:hover {
             color: rgb(192, 57, 43); 
    }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa fa-user-shield"></i> Admin Panel</h2>
        <a href="dashboard.php"><i class="fa fa-chart-pie"></i> Dashboard</a>
        <a href="manage_bookings.php"><i class="fa fa-calendar-check"></i> Manage Bookings</a>
        <a href="manage_homestays.php" class="active"><i class="fa fa-home"></i> Manage Homestays</a>
        <a href="manage_users.php"><i class="fa fa-users"></i> Manage Users</a>
        <a href="view_messages.php"><i class="fa fa-envelope"></i> View Messages</a>
        <a href="../../Homestay.php"><i class="fa fa-globe"></i> View Website</a>
        <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1><i class="fa fa-home"></i> Manage Homestays</h1>
        </div>

        <div class="homestay-table">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Owner</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($homestay = $homestays->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($homestay['profile_image']): ?>
                                <img src="../../images/<?php echo htmlspecialchars($homestay['profile_image']); ?>" alt="Homestay">
                            <?php else: ?>
                                <img src="../../images/logo.png" alt="No Image">
                            <?php endif; ?>
                        </td>
                        <td>#<?php echo $homestay['homestay_id']; ?></td>
                        <td><?php echo htmlspecialchars($homestay['name']); ?></td>
                        <td><?php echo htmlspecialchars($homestay['location']); ?></td>
                        <td><span class="price-tag">Rs. <?php echo number_format($homestay['price'], 2); ?></span></td>
                        <td><?php echo htmlspecialchars($homestay['owner_name'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="manage_homestays.php?delete_id=<?php echo $homestay['homestay_id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this homestay?')">
                               <i class="fa fa-trash"></i> Delete
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