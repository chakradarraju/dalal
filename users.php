<?php

require_once("db.php");

function register($userName,$password) {
//	$userName = mysql_real_escape_string($userName);
//	$password = mysql_real_escape_string($password);
	$query = "SELECT `userName` from `users` where `userName` = '{$userName}`";
	$result = mysql_query($query) or die($query);
	if(!mysql_fetch_assoc($result)) {
		$password = md5($password);
		$query = "INSERT INTO `users` VALUES('{$userName}','{$password}')";
		$result = mysql_query($query) or die($query);
		if(!$result) return "Error in registering user, Try after some time.";
		else return "User successfully registered";
	} else return "User with {$userName} already registered, choose another name.";
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
//	$userName = mysql_real_escape_string($userName);
//	$password = mysql_real_escape_string($password);
	$query = "SELECT `userId`,`password` FROM `users` WHERE `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
	if(!$result) return "Wrong username, '{$userName}'";
	$row = mysql_fetch_assoc($result);
	if($row['password']!=md5($password)) return "Wrong password";
	session_start();
	$_SESSION['userId'] = $row['userId'];
}

function logout() {
    session_start();
    session_destroy();
}

function changePassword($userName,$password) {
//	$userName = mysql_real_escape_string($userName);
//	$password = mysql_real_escape_string($password);
	$query = "SELECT `userName` FROM `users` WHERE `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
	if(!$result) return "Wrong username, '{$userName}'";
	$password = md5($password);
	$query = "UPDATE `users` SET `password` = '{$password}' WHERE `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
	if(!$result) return "Error in changing password, try again later";
	return "Password successfully changed";
}

function userExists($userName) {
//	$userName = mysql_real_escape_string($userName);
	$query = "SELECT `userName` FROM `users` WHERE `userName` = '{$userName}'";
	$result = mysql_query($query) or die($query);
	return $result;
}

function userIdExists($userId) {
//	$userId = mysql_real_escape_string($userId);
	$query = "SELECT `userId` FROM `users` WHERE `userId` = '{$userId}'";
	$result = mysql_query($query) or die($query);
	return $result;
}

function cashInHand($userId) {
//	$userId = mysql_real_escape_string($userId);
	$query = "SELECT `cashInHand` FROM `users` WHERE `userId` = '{$userId}'";
	$result = mysql_query($query) or die($query);
	if($row = mysql_fetch_assoc($result)) return $row['cashInHand'];
	return -1;
}

function createUser($userName,$password) {
//	$userName = mysql_real_escape_string($userName);
//	$password = mysql_real_escape_string($password);
	if(userExists($userName)) return "User with name {$userName} already exists";
	$query = "INSERT INTO `users` VALUES('{$userName}','{$password}','".CASH_IN_HAND."')")
	$result = mysql_query($query);
	if(!$result) return "Problem in creating user, try again later";
	return "User created successfully";
}
?>
