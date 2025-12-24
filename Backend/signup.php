
<?php
include 'databaseconnection.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first = trim($_POST['first'] ?? '');
    $last  = trim($_POST['last'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $cpass = $_POST['cpassword'] ?? '';
    $role  = "user";

    if(empty($first) || empty($last) || empty($email) || empty($pass) || empty($cpass)){
        echo "<script>alert('Fill up all form!'); window.history.back();</script>";
        exit();
    }
    if($pass !== $cpass){
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if($result->num_rows > 0){
        echo "<script>alert('Email already registered! Please login.'); window.location.href='../Login.html';</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

        if($stmt->execute()){
            echo "<script>alert('Account created successfully!'); window.location.href='../Login.html';</script>";
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $checkEmail->close();
}
$conn->close();
?>