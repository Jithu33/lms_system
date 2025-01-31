<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
header("Location: {$role}_dashboard.php"); // Redirect to the appropriate dashboard
exit();
?>
