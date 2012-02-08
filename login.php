<?php
require_once("server/users.php");

session_start();
if(isset($_SESSION['message'])) {
    echo $_SESSION['message'] . "<br/>";
    unset($_SESSION['message']);
}
if(isset($_SESSION['error'])) {
    echo $_SESSION['error'] . "<br/>";
    unset($_SESSION['error']);
}
$userId = getLoggedInUserId();
if($userId==-1) {
    echo "You're not logged in!<br/>Click <a href='./server/fbauth.php'>here</a> to login";
} else {
    $userName = getLoggedInUserName();
    echo "You're logged in as {$userName}<br/>Click <a href='./index.php'>here</a> to go to application";
}
