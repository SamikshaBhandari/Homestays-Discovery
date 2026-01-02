<?php
session_start();
include 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login.html");
    exit();
}

$userId = $_SESSION['user_id'];

// Purano data tanne form ma dekhouna ko lagi
$query = "SELECT * FROM users WHERE user_id = '$userId'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);

// Form submit bhayepachi data update garne logic
if (isset($_POST['update_profile'])) {
    $newName = mysqli_real_escape_string($conn, $_POST['name']);
    $newEmail = mysqli_real_escape_string($conn, $_POST['email']);
    $newPhone = mysqli_real_escape_string($conn, $_POST['phone']);

    $updateQuery = "UPDATE users SET name='$newName', email='$newEmail', phone='$newPhone' WHERE user_id='$userId'";
    
    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION['name'] = $newName; // Session update gareko
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Poppins', sans-serif;
             background-color: white;
              display: flex;
               justify-content: center;
                align-items: center;
                 min-height: 100vh; 
                 margin: 0;
                 }
        .edit-card { 
            background: white; 
            padding: 30px; 
            border-radius: 15px;
             box-shadow: 0 10px 25px rgba(0,0,0,0.1);
              width: 100%;
               max-width: 400px;
             }
        h2 { 
            text-align: center; 
            color: black;
             margin-bottom: 20px; 
            }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block;
             font-size: 14px;
              color:gray;
               margin-bottom: 5px;
             }
        input {
             width: 100%;
              padding: 10px;
               border: 1px solid white;
                border-radius: 8px;
                 box-sizing: border-box;
                 }
        .btn-container {
             display: flex;
              gap: 10px;
               margin-top: 20px;
             }
        .save-btn {
             flex: 2;
              background: green;
               color: white;
                border: none;
                 padding: 10px;
                  border-radius: 8px;
                   cursor: pointer;
                    font-weight: bold;
                 }
        .cancel-btn { flex: 1;
             background: whitesmoke; 
             color: gray; 
             border: none;
              padding: 10px;
               border-radius: 8px;
                cursor: pointer;
                 text-align: center;
                  text-decoration: none;
                   font-size: 14px;
                 }
        .save-btn:hover {
             background: green; 
            }
    </style>
</head>
<body>

<div class="edit-card">
    <h2>Edit Profile</h2>
    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" placeholder="Enter phone number">
        </div>
        
        <div class="btn-container">
            <a href="profile.php" class="cancel-btn">Cancel</a>
            <button type="submit" name="update_profile" class="save-btn">Save Changes</button>
        </div>
    </form>
</div>

</body>
</html>