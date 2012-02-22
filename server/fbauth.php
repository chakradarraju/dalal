<?php
require_once("config.php");
require_once("db.php");
require_once("users.php");

session_start();

$userId = getLoggedInUserId();
if($userId!=-1) {
    $userName = getLoggedInUserName();
    $_SESSION['message'] = "User already logged in as {$userName}!";
    header("Location: ../");
}

$appId = APP_ID;
$appSecret = APP_SECRET;
$myURL = MY_URL;
$FBage = 60*60*24*30;

function fbGet($toGet, $accessToken = false) {
    if($accessToken===false) $value = @file_get_contents($toGet);
    else $value = @file_get_contents("https://graph.facebook.com/$toGet?access_token=$accessToken");
    if($value===NULL) $value = "";
    return json_decode($value,true);
}

if(isset($_GET['code'])) {
    if($_GET['state']==$_SESSION['state']) {
        $code = $_GET['code'];
        $forToken = @file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=$appId&redirect_uri=" . urlencode($myURL) . "&client_secret=$appSecret&code=$code");
        $params = NULL;
        parse_str($forToken,$params);
        if(!isset($params['access_token'])) {
            $_SESSION['error'] = "Not able to contact facebook server, try again after sometime";
            header("Location: ../index.php");
            goto endPtr;
        }
        $accessToken = $params['access_token'];
        $user = fbGet("me",$accessToken);
        $result = mysql_query("SELECT * FROM `users` WHERE `loginMethod` = 'oauth' AND `userName` = 'facebook' AND `password` = '{$user['id']}'");
        $row = mysql_fetch_assoc($result);
        if($row===false) {
            $result = mysql_query("INSERT INTO `users` VALUES(NULL,'oauth','facebook','{$user['id']}',0)");
            if(!$result) {
                $_SESSION['error'] = "Error while creating user";
                header("Location: ../index.php");
            }
        }
        loginOAuth($user['id'],"facebook");
        $userId = getLoggedInUserId();
        if($userId==-1) {
            $_SESSION['error'] = "Error while logging in";
            header("Location: ../index.php");
        }
        if($row===false) {
            mysql_query("INSERT INTO `users_data` VALUES('{$userId}',NULL,'Display_Name','{$user['name']}')");
            $details = getDefaultDetails();
            foreach($details as $detail => $value) {
                mysql_query("INSERT INTO `users_data` VALUES('{$userId}',NULL,'{$detail}','{$value}')");
            }
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
                if(!$result) {
                    $_SESSION['error'] = "database error";
                    header("Location: ../index.php");
                }
            } else {
                unset($_SESSION['userId']);
                $_SESSION['message'] = "User account seems to have been created recently, contact event managers to verify account";
                header("Location: ../index.php");
            }
        } else {
            $_SESSION['message'] = "User {$user['name']}, successfully logged in";
            header("Location: ../index.php");
        }
    } else {
        $_SESSION['message'] = "Something went wrong";
        header("Location: ../index.php");
    }
    header("Location: ../index.php");
} else {
    $_SESSION['state'] = md5(uniqid(rand(), TRUE));
    echo("<script> top.location.href='http://www.facebook.com/dialog/oauth?client_id=$appId&redirect_uri=" . urlencode($myURL) . "&state={$_SESSION['state']}&scope=read_stream' </script>");
}
endPtr:
?>
