<?php
require_once("users.php");
require_once("stock.php");
require_once("common.php");

function mortgage($stockId, $number, $value) {
    $userId = getLoggedInUserId();
    if($userId==-1) return array("error" => "Your session expired<br />Please login again");
    $result = mysql_query("SELECT `marketValue` FROM `stocks` WHERE `stockId` = '{$stockId}'");
    if($row = mysql_fetch_assoc($result)) {
        $marketValue = $row['marketValue'];
    } else {
        return array("error" => "stock not found in database");
    }
    $numberInHand = sharesInHand($userId, $stockId);
    if($numberInHand==-1) return array("error" => "Some error in checking number of shares you hold");
    if($numberInHand<$number) {
        return array("error" => "You dont have enough shares");
    }
    if($marketValue*$number*MORTGAGE_PERCENT/100.0<$value) {
        return array("message" => "The property is lower in cost than the value quoted");
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
    $userId = getLoggedInUserId();
    if($userId==-1) return array("error" => "Your session expired<br />Please login again");
    $result = mysql_query("SELECT * FROM `bank` WHERE `mortgageId` = '{$mortgageId}'");
    if($row = mysql_fetch_assoc($result)) {
        if($row['userId']!=$userId) {
            return array("message" => "The mortgage doesn't seemed to be yours");
        }
        $mortgage = $row;
    } else {
        return array("message" => "Mortgage not found in database");
    }
    $cashInHand = cashInHand($userId);
    if($cashInHand==-1) return array("error" => "Some error in checking your cash in hand");
    if($cashInHand<$mortgage['loanValue']) {
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

function getUserMortgages($userId = NULL) {
    if($userId===NULL) $userId = getLoggedInUserId();
    if($userId==-1) return array("error" => "Your session expired<br /> Please login again");
    $result = mysql_query("SELECT `mortgageId`, `stockId`, `number`, `loanValue` FROM `bank` WHERE `userId` = '{$userId}'");
    $return = array();
    while($row = mysql_fetch_assoc($result)) {
        $return[] = $row;
    }
    return $return;
}

?>
