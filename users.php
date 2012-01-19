<?php

require_once("db.php");

function register($userName,$password) {
	$query = "SELECT `userName` from `users` where `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
	if(!mysql_fetch_assoc($result)) {
		$password = md5($password);
		$query = "INSERT INTO `users` VALUES(NULL,'{$userName}','{$password}')";
		$result = mysql_query($query) or die($query);
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

function login($userName,$password) {
	$query = "SELECT `userId`,`password` FROM `users` WHERE `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
	if(!$result) return array("error" => "Wrong username, '{$userName}'");
	$row = mysql_fetch_assoc($result);
	if($row['password']!=md5($password)) return array("error" => "Wrong password");
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
	$result = mysql_query($query) or die($query);
	if(!$result) return array("error" => "Wrong username, '{$userName}'");
	$password = md5($password);
	$query = "UPDATE `users` SET `password` = '{$password}' WHERE `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
	if(!$result) return array("error" => "Error in changing password, try again later");
	return array("message" => "Password successfully changed");
}

function userExists($userName) {
	$query = "SELECT `userName` FROM `users` WHERE `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
    if($result===NULL) return false;
    if(mysql_num_rows($result)>0) return true;
    else return false;
}

function userIdExists($userId) {
	$query = "SELECT `userId` FROM `users` WHERE `userId` = '{$userId}'";
	$result = mysql_query($query) or die($query);
    if($result===NULL) return false;
    if($mysql_num_rows($result)>0) return true;
    else return false;
}

function cashInHand($userId) {
	$query = "SELECT `value` FROM `users_data` WHERE `userId` = '{$userId}' AND `key` = 'cashInHand'";
	$result = mysql_query($query) or die($query);
	if($row = mysql_fetch_assoc($result)) return $row['value'];
	return -1;
}

/**
 * function sharesInHand(userId,stockId)
 * uses "holding" table to return the number of stocks held by given user
 * return -1 in case of any error
 **/
function sharesInHand($userId,$stockId) {
	$query = "SELECT `value` FROM `users_data` WHERE `userId` = '{$userId}' AND `key` = '{$stockId}'";
	$result = mysql_query($query);
	if(!$result) return -1;
	if(mysql_num_rows($result)==0) {
		$insQuery = "INSERT INTO `users_data` VALUES('{$userId}',NULL,'{$stockId}','0')";
		if(!mysql_query($insQuery)) return -1;
		return 0;
	}
	$row = mysql_fetch_assoc($result);
	return $row['num'];
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
