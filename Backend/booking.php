<?php
include 'databaseconnection.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' ) {
    http_response_code(405);
    die("Only POST method allowed");
}
$nights = $_POST['nights'] ?? '';
$checkin = $_POST['checkin'] ?? '';
$Fname = $_POST['Fname'] ?? '';
$guest = $_POST['guest'] ?? '';
$Email = $_POST['Email'] ?? '';
$phone = $_POST['phone'] ?? '';
echo $nights, $checkin, $Fname, $guest, $Email, $phone;



?>