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
    } else {
        echo "<script>alert('Error adding homestay');</script>";
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
    .header h1 { color: rgb(44, 62, 80); }
        .add-form { 
        background: rgb(255, 255, 255); 
        padding: 25px; 
        border-radius: 10px; 
        margin-bottom: 30px; 
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
    }
    .add-form h3 { 
        margin-bottom: 15px; 
        color: rgb(44, 62, 80); 
        border-bottom: 1px solid rgb(238, 238, 238); 
        padding-bottom: 10px; 
    }
    .form-grid {
         display: grid;
          grid-template-columns: 1fr 1fr;
           gap: 15px; 
        }
    .add-form input, .add-form textarea { 
        width: 100%; 
        padding: 12px; 
        margin-top: 5px; 
        border: 1px solid rgb(236, 240, 241); 
        border-radius: 5px; 
        background: rgb(248, 249, 250); 
        color: rgb(44, 62, 80);
    }
        .btn-add { 
        background: rgb(39, 174, 96); 
        color: rgb(255, 255, 255); 
        padding: 12px 25px; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        margin-top: 15px; 
        font-weight: bold; 
        font-size: 14px; 
    }
    .btn-add:hover {
         background: rgb(34, 153, 84);
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

        <div class="add-form">
            <h3><i class="fa fa-plus-circle"></i> Add New Homestay</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label>Homestay Name</label>
                        <input type="text" name="name" required placeholder="e.g. Moonlight Homestay">
                    </div>
                    <div>
                        <label>Location</label>
                        <input type="text" name="location" required placeholder="e.g. Lakeside, Pokhara">
                    </div>
                    <div>
                        <label>Price Per Night (Rs.)</label>
                        <input type="number" name="price" required placeholder="e.g. 1500">
                    </div>
                    <div>
                        <label>Owner Name</label>
                        <input type="text" name="owner_name" required placeholder="e.g. Ram Bahadur">
                    </div>
                    <div style="grid-column: span 2;">
                        <label>Homestay Image</label>
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Enter details about facilities, rooms, etc."></textarea>
                </div>
                <button type="submit" name="add_homestay" class="btn-add">
                    <i class="fa fa-save"></i> Save Homestay
                </button>
            </form>
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
                    <?php if($homestays->num_rows > 0): ?>
                        <?php while($homestay = $homestays->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="../../images/<?php echo !empty($homestay['profile_image']) ? htmlspecialchars($homestay['profile_image']) : 'logo.png'; ?>" alt="Homestay">
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
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No homestays found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>