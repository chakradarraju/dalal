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
#login_but.loggedin {
background: #FFFFFF;
}
</style>
<body>
<div id='login_container'>
<?php
require_once("server/users.php");

session_start();
echo json_encode($_SESSION);
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
    echo "<a href='./server/fbauth.php'><div id='login_but'></div></a>";
} else {
$userName = getLoggedInUserName();
    echo "<div id='login_but' class='loggedin'>You're logged in as {$userName}<br/>Click <a href='./index.php'>here</a> to go to application</div>";
}
?>
</div>
</body>
</html>
