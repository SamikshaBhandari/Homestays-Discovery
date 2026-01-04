<?php
include './databaseconnection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $n_pass = $_POST['n_pass'];
    $c_pass = $_POST['c_pass'];
    $user_id = $_SESSION['reset_user_id'] ?? null;

    if (!$user_id) {
        echo "<script>alert('Unauthorized! Start again.'); window.location.href='../Forgot.html';</script>";
        exit();
    }
    if ($n_pass !== $c_pass) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }
    $hashed = password_hash($n_pass, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed, $user_id);

    if ($stmt->execute()) {
        session_destroy(); //
        echo "<script>alert('Password updated successfully! Please login.'); window.location.href='../Login.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>