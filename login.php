<?php
require_once("server/users.php");
$userId = getLoggedInUserId();
if($userId!=-1) header("Location: index.php");
?>
<html>
<head><title>Login</title></head>
<style type="text/css">
#login_container{
width:1000px;
height:353px;
background:url('images/login.jpg') no-repeat;
margin:0 auto;
margin-top:150px;
}
body{
background-color:#b7e5ff;
}
#login_but{
width:200px;
height:45px;
position:relative;
left:740px;
top:25px;
cursor:pointer;
}
</style>
<body>
<div id='login_container'>
<?php

session_start();
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if(isset($_SESSION['error'])) {
    $message = $_SESSION['error'];
    unset($_SESSION['error']);
}
if($message!="") {
?>
<script type="text/javascript"> alert('<?php echo $message; ?>'); </script>
<?php
}
?>
<a href='./server/fbauth.php'><div id='login_but'></div></a>
</div>
</body>
</html>
