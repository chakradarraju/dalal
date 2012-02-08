<?php
require_once("users.php");
$userId = getLoggedInUserId();
if(isset($_POST['saveProfile'])) {
    unset($_POST['saveProfile']);
    foreach($_POST as $field => $value) {
        $field = mysql_real_escape_string($field);
        $value = mysql_real_escape_string($value);
        if($value=="") continue;
        $result = mysql_query("SELECT `value` FROM `users_data` WHERE `userId` = '{$userId}' AND `key` = '{$field}'");
        if(mysql_num_rows($result)>0) {
            mysql_query("UPDATE `users_data` SET `value` = '{$value}' WHERE `userId` = '{$userId}' AND `key` = '{$field}'");
        } else {
            mysql_query("INSERT INTO `users_data` VALUES('{$userId}',NULL,'{$field}','{$value}')");
        }
    }
}
header("Location: ../index.php");
?>
