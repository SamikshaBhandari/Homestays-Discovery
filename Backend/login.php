<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Only POST method allowed");
}
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$email = $_POST['email'] ?? '';
$password_input = $_POST['password'] ?? '';
if (empty($email) || empty($password_input)) {
    echo "Please fill in both email and password.";
    exit();
}
$stmt = $conn->prepare(
    "SELECT user_id, name, email, password, role, created_at
     FROM users
     WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password_input, $row['password'])) {
        $_SESSION['user_id']= $row['user_id'];
        $_SESSION['name']= $row['name'];
        $_SESSION['email']= $row['email'];
        $_SESSION['role']= $row['role'];
        $_SESSION['created_at'] = $row['created_at'];
        header("Location:../index1.html");
        exit();
    } else {
        echo "Invalid password.";
    }
} else {
    echo "No user found with this email address.";
}
$stmt->close();
$conn->close();
?>
