<?php
include './databaseconnection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    if (empty($email)) {
        echo "<script>alert('Please enter your email!'); window.history.back();</script>";
        exit();
    }
    $sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['reset_user_id'] = $user['user_id'];
        
        echo "<script>
                alert('Email Verified! Please set your new password.');
                window.location.href = '../ResetPassword.php'; 
              </script>";
    } else {
        echo "<script>
                alert('No account found with this email. Please sign up.');
                window.history.back();
              </script>";
    }
    $stmt->close();
}
$conn->close();
?>