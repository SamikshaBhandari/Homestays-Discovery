<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login.html");
    exit;
}

$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM homestays WHERE homestay_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Homestay deleted successfully'); window.location.href='manage_homestays.php';</script>";
    }
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_homestay'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price = (float)$_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $owner_name = mysqli_real_escape_string($conn, $_POST['owner_name']);
    
    $image = $_FILES['image']['name'];
    $temp_name = $_FILES['image']['tmp_name'];
    $target = "../../images/" . basename($image);

    $sql = "INSERT INTO homestays (name, location, price, description, profile_image, owner_name) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsss", $name, $location, $price, $description, $image, $owner_name);

    if ($stmt->execute()) {
        if (!empty($image)) {
            move_uploaded_file($temp_name, $target);
        }
        echo "<script>alert('New Homestay added successfully!'); window.location.href='manage_homestays.php';</script>";
    }
    $stmt->close();
}

$homestays = $conn->query("SELECT * FROM homestays ORDER BY homestay_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Homestays </title>
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
            width: 40px;
             height: 40px;
              border-radius: 50%; 
     }
        .form-card { 
            background-color: rgba(255, 255, 255, 1); 
            padding: 20px; 
            border: 1px solid rgba(0, 0, 0, 0.1); 
            margin-bottom: 30px;
            max-width: 550px; 
        }
        .form-card h3 {
             margin: 0 0 15px 0;
              font-size: 16px; 
              color: rgba(44, 62, 80, 1);
               border-bottom: 1px solid rgba(0,0,0,0.05);
                padding-bottom: 8px;
             }

        .form-grid { 
            display: grid;
             grid-template-columns: 1fr 1fr;
              gap: 12px;
             }
        .form-group { 
            margin-bottom: 10px; 
        }
        .form-group.full {
             grid-column: span 2;
             }
        
        label {
             font-size: 12px;
              font-weight: bold;
               color: rgba(127, 140, 141, 1);
                display: block;
                 margin-bottom: 4px;
                 }
        input, textarea { 
            width: 100%;
             padding: 8px; 
             border: 1px solid rgba(0,0,0,0.1); 
            border-radius: 4px;
             background: rgba(248, 249, 250, 1);
            font-size: 13px;
             box-sizing: border-box;
        }
        .btn-save { 
            background-color: rgba(39, 174, 96, 1);
             color: white;
             border: none; 
            padding: 10px 20px;
             cursor: pointer; 
             border-radius: 4px; 
             font-weight: bold;
              font-size: 13px;
        }
        .table-card {
             background-color: rgba(255, 255, 255, 1);
              border: 1px solid rgba(0, 0, 0, 0.1);
             }
        table {
             width: 100%;
              border-collapse: collapse;
             }
        th, td {
             padding: 12px;
              text-align: left;
               border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                font-size: 13px;
             }
        th {
             background-color: rgba(248, 249, 250, 1);
              color: rgba(127, 140, 141, 1);
             }

        .thumb {
             width: 45px;
              height: 45px; 
              object-fit: cover;
               border-radius: 4px;
                border: 1px solid rgba(0,0,0,0.1);
             }
        .btn-delete { 
            color: rgba(231, 76, 60, 1);
             text-decoration: none;
              font-weight: bold;
             }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_bookings.php">Manage Bookings</a>
        <a href="manage_homestays.php" class="active">Manage Homestays</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="view_messages.php">View Messages</a>
        <a href="../../Homestay.php">Public Site</a>
        <a href="../logout.php" style="color: rgba(231, 76, 60, 1);">Logout</a>
    </div>

    <div class="main">
        <div class="top-nav">
            <h3 style="margin:0; color: rgba(44, 62, 80, 1);">Homestay Inventory</h3>
            <div class="admin-profile">
                <div style="text-align: right;">
                    <b style="display:block; font-size:14px;"><?php echo htmlspecialchars($userName); ?></b>
                    <small style="color:rgba(127,140,141,1);">Admin</small>
                </div>
                <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($userEmail))); ?>?d=mp&s=40" alt="Admin">
            </div>
        </div>

        <div class="form-card">
            <h3>Add New Homestay</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Homestay Name</label>
                        <input type="text" name="name" required placeholder="">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" required placeholder="">
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" name="price" required placeholder="">
                    </div>
                    <div class="form-group">
                        <label>Owner Name</label>
                        <input type="text" name="owner_name" required placeholder="">
                    </div>
                    <div class="form-group full">
                        <label>Featured Image</label>
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group full">
                        <label>Description (Optional)</label>
                        <textarea name="description" rows="2" placeholder="Brief details about the place..."></textarea>
                    </div>
                </div>
                <button type="submit" name="add_homestay" class="btn-save">Save Homestay</button>
            </form>
        </div>

        <div class="table-card">
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
                    <?php if($homestays->num_rows > 0): ?>
                        <?php while($h = $homestays->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../../images/<?php echo !empty($h['profile_image']) ? htmlspecialchars($h['profile_image']) : 'logo.png'; ?>" class="thumb"></td>
                            <td>#<?php echo $h['homestay_id']; ?></td>
                            <td><b><?php echo htmlspecialchars($h['name']); ?></b></td>
                            <td><?php echo htmlspecialchars($h['location']); ?></td>
                            <td style="color:rgba(39, 174, 96, 1); font-weight:bold;">Rs. <?php echo number_format($h['price']); ?></td>
                            <td><?php echo htmlspecialchars($h['owner_name'] ?? 'N/A'); ?></td>
                            <td>
                                <a href="manage_homestays.php?delete_id=<?php echo $h['homestay_id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Delete this record?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding:20px;">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
<?php $conn->close(); ?>