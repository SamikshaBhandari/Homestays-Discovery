<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms";

$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
$first = isset($_POST["first"]) ? trim($_POST["first"]) : "";
$last = isset($_POST["last"]) ? trim($_POST["last"]) : "";
$name = $first . " " . $last;
$email = isset($_POST["Email"]) ? trim($_POST["Email"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$cpassword = isset($_POST["cpassword"]) ? trim($_POST["cpassword"]) : "";
$role = "user"; 

if(empty($first) || empty($last) || empty($email) || empty($password) || empty($cpassword)){
    echo "Please fill in all fields.";
    exit();
}
if($password !== $cpassword){
    echo "Passwords do not match.";
    exit();
}
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    echo "This email is already registered. Please login.";
} else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

    if($stmt->execute()){
        header("Location: Login.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
$stmt->close();
$conn->close();
?>