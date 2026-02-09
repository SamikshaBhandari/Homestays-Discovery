<?php
session_start();
include 'databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE user_id = '$userId'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: white;
            color: gray;
        }
        .profile-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .profile-card {
            background: white;
            width: 100%;
            max-width: 480px; 
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
        }
        .back-home {
            position: absolute;
            top: 25px;
            left: 25px;
            text-decoration: none;
            color: gray;
            font-size: 20px;
            transition: 0.3s;
        }
        .back-home:hover {
            color: blue;
            transform: translateX(-3px);
        }
        .avatar-section {
            margin-bottom: 20px;
        }
        .user-avatar-big {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, silver, silver); 
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            color: white;
            font-size: 50px;
            font-weight: bold;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        .profile-card h2 {
            margin: 10px 0 5px;
            color: black;
        }
       .profile-card .tag {
            color: gray;
            font-size: 14px;
            background: white;
            padding: 3px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 25px;
        }
        .info-group {
            text-align: left;
            margin-bottom: 20px;
            border-bottom: 1px solid gray;
            padding-bottom: 10px;
        }
        .info-group label {
            display: block;
            font-size: 12px;
            color: gray;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-group p {
            margin: 0;
            font-size: 16px;
            color: black;
            font-weight: 500;
        }
        .profile-btns {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 30px;
        }
        .btn {
            flex: 1; 
            padding: 12px 5px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: 0.3s;
            white-space: nowrap;
        }
        .edit-btn {
            background-color: blue;
            color: white;
        }
        .logout-btn {
            background-color: red;
            color: white;
        }
        .delete-btn {
            background-color: rgb(51, 51, 51);
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <a href="../index1.php" class="back-home" title="Back to Home">
                <i class="fas fa-arrow-left"></i>
            </a>           
            <div class="avatar-section">
                <div class="user-avatar-big">
                    <?php echo strtoupper(substr($userData['name'], 0, 1)); ?>
                </div>
            </div>
            <h2><?php echo htmlspecialchars($userData['name']); ?></h2>
            <span class="tag">Traveller / Explorer</span>
            
            <div class="info-group">
                <label>Full Name</label>
                <p><?php echo htmlspecialchars($userData['name']); ?></p>
            </div>
            <div class="info-group">
                <label>Email Address</label>
                <p><?php echo htmlspecialchars($userData['email']); ?></p>
            </div>           
            <div class="info-group">
                <label>Phone Number</label>
                <p><?php echo htmlspecialchars($userData['phone'] ?? 'Not provided'); ?></p>
            </div>

            <div class="profile-btns">
                <button class="btn edit-btn" onclick="location.href='Edit.php'">
                    <i class="fas fa-edit"></i> Edit
                </button>               
                
                <button class="btn logout-btn" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>

                <button class="btn delete-btn" id="deleteAccountBtn">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("logoutBtn").addEventListener("click", function () {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        });
        document.getElementById("deleteAccountBtn").addEventListener("click", function () {
            if (confirm("DANGER: Delete your account permanently?")) {
                if (confirm("Final confirmation: This cannot be undone. Proceed?")) {
                    window.location.href = "delete_account.php";
                }
            }
        });
    </script>
</body>
</html>