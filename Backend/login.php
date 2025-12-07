<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

$email = isset($_POST["email"]) ? $_POST["email"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if(empty($email) || empty($password)){
    echo "Please fill in both email and password.";
    exit();
}
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    if(password_verify($password, $row['password'])){
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
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