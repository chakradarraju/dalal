<?php

require_once("db.php");

function register($userName,$password) {
    $query = "SELECT `userName` from `users` where `userName` = '{$userName}'";
    $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
    if(!mysql_fetch_assoc($result)) {
        $password = md5($password);
        $query = "INSERT INTO `users` VALUES(NULL,'{$userName}','{$password}',0)";
        $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
        $useridresult = mysql_query("SELECT `userId` FROM `users` WHERE `userName` = '{$userName}'");
        $useridrow = mysql_fetch_assoc($useridresult);
        $userId = $useridrow['userId'];
        $defaultDetails = getDefaultDetails();
        foreach($defaultDetails as $key => $value) {
            mysql_query("INSERT INTO `users_data` VALUES('{$userId}',NULL,'{$key}','{$value}')");
        }
        if(!$result) return array("error" => "Error in registering user, Try after some time.");
        else return array("message" => "User successfully registered");
    } else return array("error" => "User with {$userName} already registered, choose another name.");
}

function checkLogin() {
    session_start();
    return isset($_SESSION['userId']);
}

function getLoggedInUserId() {
    session_start();
    if(!isset($_SESSION['userId'])) return -1;
    return $_SESSION['userId'];
}

function getLoggedInUserName() {
    session_start();
    if(!isset($_SESSION['userId'])) return "";
    $userId = $_SESSION['userId'];
    $result = mysql_query("SELECT `value` FROM `users_data` WHERE `userId` = '{$userId}' AND `key` = 'Display_Name'");
    if($row = mysql_fetch_assoc($result)) return $row['value'];
    return "";
}

function logindb($userName,$password) {
    $query = "SELECT `userId`,`password` FROM `users` WHERE `userName` = '{$userName}' AND `loginMethod` = 'db'";
    $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
    if(!$result) return array("error" => "Wrong username, '{$userName}'");
    $row = mysql_fetch_assoc($result);
    if($row['password']!=md5($password)) return array("error" => "Wrong password");
    session_start();
    $_SESSION['userId'] = $row['userId'];
    return array("message" => "User {$userName}, successfully logged in");
}

function loginOAuth($oauthId,$oauthProvider) {
    $query = "SELECT * FROM `users` WHERE `userName` = '{$oauthProvider}' AND `password` = '{$oauthId}'";
    $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
    if(!$result) return array("error" => "User not found in database");
    $row = mysql_fetch_assoc($result);
    session_start();
    $_SESSION['userId'] = $row['userId'];
    return array("message" => "User {$userName}, successfully logged in");
}

function logout() {
    session_start();
    session_destroy();
}

function changePassword($userName,$password) {
    $query = "SELECT `userName` FROM `users` WHERE `userName` = '{$userName}'";
    $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
    if(!$result) return array("error" => "Wrong username, '{$userName}'");
    $password = md5($password);
    $query = "UPDATE `users` SET `password` = '{$password}' WHERE `userName` = '{$userName}'";
    $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
    if(!$result) return array("error" => "Error in changing password, try again later");
    return array("message" => "Password successfully changed");
}

function userExists($userName) {
    $query = "SELECT `userName` FROM `users` WHERE `userName` = '{$userName}'";
    $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
    if($result===NULL) return false;
    if(mysql_num_rows($result)>0) return true;
    else return false;
}

function userIdExists($userId) {
    if($userId==THE_TRADER) return true;
    $query = "SELECT `userId` FROM `users` WHERE `userId` = '{$userId}'";
    $result = mysql_query($query) or die(json_encode(array("error" => "Database error")));
    if($result===NULL) return false;
    if(mysql_num_rows($result)>0) return true;
    else return false;
}

function cashInHand($userId) {
    if($userId==THE_TRADER) return 10000000;
    $query = "SELECT `value` FROM `users_data` WHERE `userId` = '{$userId}' AND `key` = 'cashInHand'";
    $result = mysql_query($query);
    if(!$result) return -1;
    if($row = mysql_fetch_assoc($result)) return $row['value'];
    return -1;
}

/**
 * function sharesInHand(userId,stockId)
 * uses "holding" table to return the number of stocks held by given user
 * return -1 in case of any error
 **/
function sharesInHand($userId,$stockId) {
    if($userId==THE_TRADER) return 100000000;
    $query = "SELECT `value` FROM `users_data` WHERE `userId` = '{$userId}' AND `key` = '{$stockId}'";
    $result = mysql_query($query);
    if(!$result) return -1;
    if(mysql_num_rows($result)==0) {
        $insQuery = "INSERT INTO `users_data` VALUES('{$userId}',NULL,'{$stockId}','0')";
        if(!mysql_query($insQuery)) return -1;
        return 0;
    }
    $row = mysql_fetch_assoc($result);
    return $row['value'];
}

function isUserProfileFilled($userId) {
    $profileFields = PROFILE_FIELDS;
    $result = mysql_query("SELECT COUNT(DISTINCT(`key`)) AS 'count' FROM `users_data` WHERE `userId` = '{$userId}' AND `key` IN ({$profileFields})");
    if($row = mysql_fetch_assoc($result)) {
        $brokenFields = explode(",",$profileFields);
        return $row['count']>=count($brokenFields);
    }
    return false;
}

function getProfileForm($userId) {
    $profileFields = PROFILE_FIELDS;
    $brokenFields = explode(",",$profileFields);
    $fieldValues = array();
    $result = mysql_query("SELECT `key`, `value` FROM `users_data` WHERE `userId` = '{$userId}' AND `key` IN ({$profileFields})");
    while($row = mysql_fetch_assoc($result)) {
        $fieldValues[$row['key']] = $row['value'];
    }
    $return = "<form action='./server/profileSubmit.php' method=POST>";
    foreach($brokenFields as $field) {
        $field = trim($field,"'");
        $thisFieldValue = "";
        if(isset($fieldValues[$field])) $thisFieldValue = $fieldValues[$field];
        $return .= "\n{$field}: <input type='text' name='{$field}' value='{$thisFieldValue}' /><br/>";
    }
    $return .= "\n<input type='submit' name='saveProfile' value='Save Profile' />";
    return $return;
}

function getUserHiddenDetails() {
    $hiddenDetails = array();
    return $hiddenDetails;
}

function getDefaultDetails() {
    $defaults = array("cashInHand" => CASH_IN_HAND);
    return $defaults;
}
?>
