<?php
require_once("users.php");
logout();
session_start();
$_SESSION['message'] = "You've been logged out";
header("Location: ../login.php");
?>
