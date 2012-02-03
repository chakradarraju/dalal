<?php
require_once("config.php");
require_once("db.php");
require_once("users.php");

$appId = APP_ID;
$appSecret = APP_SECRET;
$myURL = MY_URL;
$FBage = 60*60*24*30;

session_start();
$code = $_GET['code'];
if(empty($code)) {
    $_SESSION['state'] = md5(uniqid(rand(), TRUE));
    echo("<script> top.location.href='http://www.facebook.com/dialog/oauth?client_id=$appId&redirect_uri=" . urlencode($myURL) . "&state={$_SESSION['state']}&scope=read_stream' </script>");
}

function fbGet($toGet, $accessToken = false) {
    if($accessToken===false) $value = @file_get_contents($toGet);
    else $value = @file_get_contents("https://graph.facebook.com/$toGet?access_token=$accessToken");
    if($value===NULL) $value = "";
    return json_decode($value,true);
}

if($_GET['state']==$_SESSION['state']) {
    $forToken = @file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=$appId&redirect_uri=" . urlencode($myURL) . "&client_secret=$appSecret&code=$code");
    $params = NULL;
    parse_str($forToken,$params);
    $accessToken = $params['access_token'];
    $user = fbGet("me",$accessToken);
    $result = mysql_query("SELECT * FROM `users` WHERE `loginMethod` = 'oauth' AND `userName` = 'facebook' AND `password` = '{$user['id']}'");
    $row = mysql_fetch_assoc($result);
    if($row===false) {
        $result = mysql_query("INSERT INTO `users` VALUES(NULL,'oauth','facebook','{$user['id']}',0)");
        if(!$result) die(json_encode(array("error" => "Error while creating user")));
    }
    loginOAuth($user['id'],"facebook");
    $userId = getLoggedInUserId();
    die(json_encode(array("error" => "Error while Logging in")));
    if($row===false) {
        mysql_query("INSERT INTO `users_data` VALUES('{$userId}',NULL,'Display Name','{$user['name']}')");
        mysql_query("INSERT INTO `users_data` VALUES('{$userId}',NULL,'cashInHand','".CASH_IN_HAND."')");
    }
    $verified = true;
    if($row===false||$row['verified']==0) {
        $posts = fbGet("me/statuses",$accessToken);
        $ok = false; $oldest = 0;
        while(!$ok&count($posts['data'])>0) {
            foreach($posts['data'] as $post) {
                $oldest = max($oldest,time()-strtotime($post['updated_time']));
                if($oldest>=$FBage) $ok = true;
                if($ok) break;
            }
            if($ok) break;
            $posts = fbGet($posts['paging']['next']);
        }
        if($ok) {
            $result = mysql_query("UPDATE `users` SET `verified` = 1 WHERE `userId` = '{$userId}'");
            if(!$result) die(json_encode(array("error" => "database error")));
        } else {
            logout();
            die(json_encode(array("message" => "User account seems to have been created recently, contact event managers to verify account")));
        }
    }
    die(json_encode(array("message" => "User {$user['name']}, successfully logged in")));
} else {
    die(json_encode(array("message" => "Something went wrong")));
}
?>
