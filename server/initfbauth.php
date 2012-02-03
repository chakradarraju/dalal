<?php

require_once("config.php");

$appId = APP_ID;
$appSecret = APP_SECRET;
$myURL = MY_URL;

session_start();
$_SESSION['state'] = md5(uniqid(rand(), TRUE));
echo("<script> top.location.href='http://www.facebook.com/dialog/oauth?client_id=$appId&redirect_uri=" . urlencode($myURL) . "&state={$_SESSION['state']}&scope=read_stream' </script>");

?>
