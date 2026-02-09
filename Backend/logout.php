<?php
session_start();
session_unset();
session_destroy();
setcookie("isLoggedIn", "", time() - 3600, "/");
header("Location: ../index1.php");
exit;
?>