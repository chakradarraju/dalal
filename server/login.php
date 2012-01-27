<?php
if(isset($_GET['logout'])) {
    require_once("users.php");
    logout();
}
if(isset($_POST['username'])) {
    require_once("users.php");
    if(isset($_POST['register'])) $result = register($_POST['username'],$_POST['password']);
    else $result = login($_POST['username'],$_POST['password']);
    if(isset($result['error'])) {
        echo $result['error'] . "<br/>";
    } else if(isset($result['message'])) {
        die($result['message'] . "<br/>");
    } else {
        echo "Some problem in login, try again later";
    }
}
session_start();
if(isset($_SESSION['userId'])) echo "Logged in...<br/>";
?>
<html>
<head>
<title></title>
</head>
<body>
<form action="login.php" method=POST>
Username:<input type=text name='username' /><br/>
Password:<input type=password name='password' /><br/>
<input type='checkbox' name='register' /><br/>
<input type='submit' value='Login'/>
</form>
</body>
</html>
