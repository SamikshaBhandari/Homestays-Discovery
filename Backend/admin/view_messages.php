<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Login.html');
    exit;
}

$messages_sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$messages_result = mysqli_query($conn, $messages_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Contact Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <style>
      * {
         margin: 0;
       padding: 0;
        box-sizing: border-box;
     }
      body {
         font-family: Arial, sans-serif;
          background: rgb(248, 249, 250);
           padding: 20px;
         }
      .container {
         max-width: 1200px; 
         margin: 0 auto;
         }
      h1 {
         color: rgb(44, 62, 80);
          margin-bottom: 30px; 
          text-align: center;
         }
      .message-card {
         background: white;
          padding: 20px;
           margin-bottom: 20px;
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
         }
      .message-header { 
        display: flex;
         justify-content: space-between;
          align-items: center;
           margin-bottom: 15px; 
           border-bottom: 2px solid rgb(240, 240, 240);
            padding-bottom: 10px;
         }
      .message-header h3 {
         color: rgb(44, 62, 80); margin: 0;
         }
      .message-date { 
        color: rgb(127, 140, 141);
         font-size: 14px;
         }
      .message-info {
         display: grid;
          grid-template-columns: repeat(3, 1fr);
           gap: 15px; 
           margin-bottom: 15px;
         }
      .info-item {
         color: rgb(85, 85, 85); 
         font-size: 14px; 
        }
      .info-item i { 
        color: rgb(52, 152, 219);
       margin-right: 5px;
     }
      .message-text {
         color: rgb(85, 85, 85); 
         line-height: 1.6;
          background: rgb(248, 249, 250); 
          padding: 15px; 
          border-radius: 6px;
         }
      .no-messages { 
        text-align: center;
         padding: 60px 20px;
          color: rgb(127, 140, 141);
         }
      .back-btn {
         display: inline-block;
          margin-bottom: 20px;
           background: rgb(23, 64, 214);
            color: white; padding: 10px 20px;
             text-decoration: none;
              border-radius: 6px;
             }
      .back-btn:hover { 
        background: rgb(15, 45, 150);
     }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
        
        <h1><i class="fa fa-envelope"></i> Contact Messages</h1>

        <?php if ($messages_result && mysqli_num_rows($messages_result) > 0): ?>
            <?php while ($msg = mysqli_fetch_assoc($messages_result)): ?>
            <div class="message-card">
                <div class="message-header">
                    <h3><?php echo htmlspecialchars($msg['subject']); ?></h3>
                    <span class="message-date">
                        <i class="fa fa-clock"></i> 
                        <?php echo date('M d, Y - h:i A', strtotime($msg['created_at'])); ?>
                    </span>
                </div>
                
                <div class="message-info">
                    <div class="info-item">
                        <i class="fa fa-user"></i>
                        <strong>Name:</strong> <?php echo htmlspecialchars($msg['full_name']); ?>
                    </div>
                    <div class="info-item">
                        <i class="fa fa-envelope"></i>
                        <strong>Email:</strong> <?php echo htmlspecialchars($msg['email']); ?>
                    </div>
                    <div class="info-item">
                        <i class="fa fa-phone"></i>
                        <strong>Phone:</strong> <?php echo htmlspecialchars($msg['phone']); ?>
                    </div>
                </div>
                
                <div class="message-text">
                    <strong>Message:</strong><br>
                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-messages">
                <i class="fa fa-inbox" style="font-size: 64px; color: rgb(189, 195, 199); margin-bottom: 20px; display: block;"></i>
                <p style="font-size: 18px;">No messages yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>