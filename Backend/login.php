<?php
session_start();
include 'databaseconnection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Login.html");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo "<script>alert('Please fill in both email and password'); window.history.back();</script>";
    exit;
}

$stmt = $conn->prepare(
    "SELECT user_id, name, email, password, role, created_at
     FROM users
     WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('User not found'); window.history.back();</script>";
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
   
    echo "<script>alert('Wrong password'); window.history.back();</script>";
    exit;
}
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['name'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['created_at'] = $user['created_at'];

setcookie("isLoggedIn", "true", time() + (86400 * 1), "/"); 
setcookie("userName", $user['name'], time() + (86400 * 1), "/");

header("Location: ../index1.html");
exit;

