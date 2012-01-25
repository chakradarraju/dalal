<?php
require_once("users.php");
require_once("stock.php");
require_once("exchange.php");

$userId = getLoggedInUserId();

function mortgage($stockId, $number, $value) {
    $result = mysql_query("SELECT `marketValue` FROM `stocks` WHERE `stockId` = '{$stockId}'");
    if($row = mysql_fetch_assoc($result)) {
        if($row['marketValue']*$number*MORTGAGE_PERCENT/100.0<$value) {
            return array("message" => "The property is lower in cost than the value quoted");
        }
    } else {
        return array("error" => "stock not found in database");
    }
    $numberInHand = sharesInHand($userId, $stockId);
    if($number<$numberInHand) {
        return array("error" => "You dont have enough shares");
    }
    $query =<<<QUERY
START TRANSACTION;
UPDATE `users_data` SET `value` = `value` - '{$number}' WHERE `userId` = '{$userId}' AND `key` = '{$stockId}';
UPDATE `users_data` SET `value` = `value` + '{$value}' WHERE `userId` = '{$userId}' AND `key` = 'cashInHand';
INSERT INTO `bank` VALUES (NULL,'{$userId}','{$stockId}','{$number}','{$value}');
COMMIT;
QUERY;
    $failed = runQueries($query);
    if($failed) {
        return array("error" => "Unknown database error occured, try again");
    }
    return array("message" => "Property mortgage for {$value} successfully");
}

function recover($mortgageId) {
    $result = mysql_query("SELECT * FROM `bank` WHERE `mortgageId` = '{$mortgage}'");
    if($row = mysql_fetch_assoc($result)) {
        if($row['userId']!=$userId) {
            return array("message" => "The mortgage doesn't seemed to be yours");
        }
        $mortgage = $row;
    } else {
        return array("message" => "Mortgage not found in database");
    }
    if(cashInHand($userId)<$mortgage['loanValue']) {
        return array("error" => "Not enough cash to pay off the mortgage");
    }
    $query =<<<QUERY
START TRANSACTION;
UPDATE `users_data` SET `value` = `value` - '{$mortgage['loanValue']}' WHERE `userId` = '{$userId}' AND `key` = 'cashInHand';
UPDATE `users_data` SET `value` = `value` + '{$mortgage['number']}' WHERE `userId` = '{$userId}' AND `key` = '{$mortgage['stockId']}';
DELETE FROM `bank` WHERE `mortgageId` = '{$mortgageId}';
COMMIT;
QUERY;
    $failed = runQueries($query);
    if($failed) {
        return array("error" => "Unknown database error occured, try again");
    }
    return array("message" => "Property bought back");
}

if(isset($_POST['mortgage'])) {
    $stockId = mysql_real_escape_string($_POST['stockId']);
    $number = mysql_real_escape_string($_POST['number']);
    $value = mysql_real_escape_string($_POST['value']);
    $return = mortgage($stockId,$number,$value);
    die(json_encode($return));
}

if(isset($_POST['recover'])) {
    $mortgageId = mysql_real_escape_string($_POST['mortgageId']);
    $return = recover($mortgageId);
    die(json_encode($return));
}

?>
