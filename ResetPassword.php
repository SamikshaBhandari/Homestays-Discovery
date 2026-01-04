<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
    <style>
        body { 
            background-color: rgb(213, 211, 208); 
            display: flex;
             justify-content: center;
              align-items: center;
               min-height: 100vh;
                font-family: "Poppins", sans-serif;
             }
        .container1 {
             width: 420px;
              padding: 35px;
               background: white;
                border-radius: 12px;
                 box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                  text-align: center;
                 }
        .input-box {
             text-align: left;
              margin-top: 20px;
               position: relative;
             }
        .input-box i {
             position: absolute;
              left: 12px;
               top: 40px; 
               color: gray;
               gap:10px;
             }
        .input-box input { 
            width: 100%;
             padding: 12px 12px 12px 40px;
             box-sizing: border-box;
              border: 1px solid gray;
               border-radius: 6px;
                margin-top: 8px;
                 outline: none;
                 }
        .btn {
             width: 100%;
              padding: 12px;
               background: rgb(33, 94, 226);
                color: white;
                 border: none;
                  border-radius: 6px;
                   font-weight: bold;
                    cursor: pointer;
                     margin-top: 20px;
                     }
    </style>
</head>
<body>
    <div class="container1">
        <h1>New Password</h1>
        <p style="color: gray; font-size: 14px;">Please create a strong new password.</p>
        <form action="Backend/update.php" method="POST">
            <div class="input-box">
                <label>New Password</label>
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="n_pass" placeholder="Enter new password" required>
            </div>
            <div class="input-box">
                <label>Confirm Password</label>
                <i class="fa-solid fa-check-circle"></i>
                <input type="password" name="c_pass" placeholder="Confirm new password" required>
            </div>
            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
</body>
</html>