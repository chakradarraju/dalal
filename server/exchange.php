<?php
require_once("users.php");
require_once("stock.php");
require_once("common.php");

/**
 * function buy(stockId,num,value)
 * Buy 'num' number of stock 'stockId' based on previous sell orders <= 'value'
 * return status of the buy request as string
 **/
function buy($stockId,$num,$value) {
    $buyId = getLoggedInUserId();
    if($buyId==-1) return array("error" => "Your session expired<br />Please login again");
    $result = mysql_query("START TRANSACTION;");
    if(!$result) return array("error" => "Database error");
    $Cnum = $num;
    $marketValue = getMarketValue($stockId);
    if($marketValue==-1) return array("error" => "Some error in checking market value");
    $query = "SELECT `sellId`,`userId`,`num`,`value` FROM `sell` WHERE `stockId` = '{$stockId}' ORDER BY `value` ASC";
    $result = mysql_query($query);
    $cutoff = 1 + CUTOFF_PERCENT/100.0;
    while($num>0&&$row=mysql_fetch_assoc($result)) {
        if($row['value']>$cutoff*$marketValue||$row['value']>$value) break;
        $cur_num = min($num,$row['num']);
        $cur_value = ($value+$row['value'])/2;
        $tradeResult = trade($row['userId'],$buyId,$stockId,$cur_num,$cur_value);
        if($tradeResult==1) {
            $num -= $cur_num;
            if($row['num']>$cur_num) {
                $query = "UPDATE `sell` SET `num` = `num` - '{$cur_num}' WHERE `sellId` = '{$row['sellId']}'";
            } else {
                $query = "DELETE FROM `sell` WHERE `sellId` = '{$row['sellId']}'";
            }
            $result = mysql_query($query);
            if(!$result) mysql_query("ROLLBACK");
        }
    }
    if($value>=$cutoff*$marketValue&&$num>0) {
        $cur_value = ($value+$cutoff*$marketValue)/2;
        $tradeResult = trade(THE_TRADER,$buyId,$stockId,$num,$cur_value);
        if($tradeResult==1) $num = 0;
        else return array("message" => ("Bought ".($Cnum-$num)));
    }
    if($num>0) {
        $insQuery = "INSERT INTO `buy` VALUES(NULL,'{$buyId}','{$stockId}','{$num}','{$value}')";
        $insResult = mysql_query($insQuery);
        $traded = $Cnum-$num;
        if(!$insResult) $returnString = "Bought {$traded}, couldn't place more orders, try again later";
        else $returnString =  "Bought {$traded}, and placed order for {$num} at the rate of {$value}";
    }
    $result = mysql_query("COMMIT;");
    if(!$result) {
        mysql_query("ROLLBACK");
        return array("error" => "Unknown error");
    }
    if($returnString) return array("message" => $returnString);
    return array("message" => "Bought {$Cnum}");
}

/**
 * function sell(stockId,num,value)
 * Sell 'num' number of stock 'stockId' based on previous buy orders >= 'value'
 * return status of the buy request as string
 **/
function sell($stockId,$num,$value) {
    $sellId = getLoggedInUserId();
    if($sellId==-1) return array("error" => "Your session expired<br /> Please login again");
    $result = mysql_query("START TRANSACTION;");
    if(!$result) return array("error" => "Database error");
    $Cnum = $num;
    $marketValue = getMarketValue($stockId);
    if($marketValue==-1) return array("error" => "Some error in checking market value");
    $cutoff = 1 - CUTOFF_PERCENT/100.0;
    if($value<$cutoff*$marketValue) {
        $cur_value = ($value+$cutoff*$marketValue)/2;
        $tradeResult = trade($sellId,THE_TRADER,$stockId,$num,$cur_value);
        if($tradeResult==1) $num = 0;
        else return array("error" => "Error processing order");
    }
    $query = "SELECT `buyId`,`userId`,`num`,`value` FROM `buy` WHERE `stockId` = '{$stockId}' ORDER BY `value` DESC";
    $result = mysql_query($query);
    while($num>0&&$row=mysql_fetch_assoc($result)) {
        if($row['value']<$value) break;
        $cur_num = min($num,$row['num']);
        $cur_value = ($value+$row['value'])/2;
        $tradeResult = trade($sellId,$row['userId'],$stockId,$cur_num,$cur_value);
        if($tradeResult==1) {
            $num -= $cur_num;
            if($row['num']>$cur_num) {
                $query = "UPDATE `buy` SET `num` = `num` - '{$cur_num}' WHERE `buyId` = '{$row['buyId']}'";
            } else {
                $query = "DELETE FROM `buy` WHERE `buyId` = '{$row['buyId']}'";
            }
            $result = mysql_query($query);
            if(!$result) mysql_query("ROLLBACK");
        }
    }
    if($num>0) {
        $insQuery = "INSERT INTO `sell` VALUES(NULL,'{$sellId}','{$stockId}','{$num}','{$value}')";
        $insResult = mysql_query($insQuery);
        $traded = $Cnum-$num;
        if(!$insResult) $returnString = "Sold {$traded}, couldn't place more orders, try again later";
        else $returnString = "Sold {$traded}, and placed order for {$num} at the rate of {$value}";
    }
    $result = mysql_query("COMMIT;");
    if(!$result) {
        mysql_query("ROLLBACK");
        return array("error" => "Unknown error");
    }
    if($returnString) return array("message" => $returnString);
    return array("message" => "Sold {$Cnum}");
}

/**
 * function trade(fromId,toId,stockId,num,value)
 * trades 'num' shares of type 'stockId' from 'fromId' user to 'toId' user
 * at the rate of 'value' per share
 * return 1 on success
 * return -1 in case of any error
 **/
function trade($fromId,$toId,$stockId,$num,$value) {
    if($fromId==$toId) return -1;
    if(!userIdExists($fromId)||!userIdExists($toId)||!stockIdExists($stockId)) return "Invalid inputs";
    $fromCash = cashInHand($fromId);
    $toCash = cashInHand($toId);
    $tradeValue = $value*$num;
    $fromNum = sharesInHand($fromId,$stockId);
    $toNum = sharesInHand($toId,$stockId);
    if($fromCash==-1||$toCash==-1||$fromNum==-1||$toNum==-1||$toCash<$tradeValue||$fromNum<$num) return -1;
    $interval = UPDATE_INTERVAL;
    $query = <<<QUERY
UPDATE `users_data` SET `value` = ROUND(`value` + '{$tradeValue}',2) WHERE `userId` = '{$fromId}' AND `key` = 'cashInHand';
UPDATE `users_data` SET `value` = ROUND(`value` - '{$tradeValue}',2) WHERE `userId` = '{$toId}' AND `key` = 'cashInHand';
UPDATE `users_data` SET `value` = `value` - '{$num}' WHERE `userId` = '{$fromId}' AND `key` = '{$stockId}';
UPDATE `users_data` SET `value` = `value` + '{$num}' WHERE `userId` = '{$toId}' AND `key` = '{$stockId}';
UPDATE `stocks` SET `lastTrade` = ROUND('{$value}',2), `marketValue` = ROUND(`marketValue` + ({$value}-`marketValue`)*{$num}/`numIssued`,2), `dayLow` = ROUND(LEAST(`dayLow`,{$value}),2), `dayHigh` = ROUND(GREATEST(`dayHigh`,{$value}),2) WHERE `stockId` = '{$stockId}';
INSERT INTO `log` VALUES(NULL,'{$fromId}','{$toId}','{$stockId}','{$num}',ROUND('{$value}',2));
INSERT INTO `stocks_data`
    SELECT '{$stockId}', NOW(), 'graph_point', `marketValue` FROM `stocks` WHERE `stockId` = '{$stockId}'
    WHERE NOT EXISTS (
        SELECT * FROM `stocks_data` WHERE `stockId` = '{$stockId}' AND `key` = 'graph_point' AND `time` > NOW() - INTERVAL {$interval}
    );
QUERY;
    $failed = runQueries($query);
    if($failed) return -1;
    return 1;
}

function buyFromExchange($shareId, $number) {
    $userId = getLoggedInUserId();
    if($userId==-1) return array("error" => "Your session expired<br />Please login again");
    $result = mysql_query("SELECT `name`,`exchangePrice`,`sharesInExchange` FROM `stocks` WHERE `stockId` = '{$shareId}'");
    $numToBuy = $number;
    $message = "";
    if($row = mysql_fetch_assoc($result)) {
        $sharesInHand = sharesInHand($userId,$shareId);
        $cashInHand = cashInHand($userId);
        if($cashInHand==-1) return array("error" => "Some error in checking your cash in hand");
        if($row['sharesInExchange']<$number) {
            $numToBuy = $row['sharesInExchange'];
            if($numToBuy>0) {
                $message = "Only $numToBuy shares of {$row['name']} were available in exchange";
            } else {
                $message = "There are no shares of {$row['name']} available in exchange now";
            }
        }
        if($cashInHand<$numToBuy*$row['exchangePrice']) {
            $numToBuy = floor($cashInHand/$row['exchangePrice']);
            $message = "You had cash enough to buy only $numToBuy shares of {$row['name']}";
        }
        $amount = $numToBuy*$row['exchangePrice'];
        $query =<<<QUERY
START TRANSACTION;
UPDATE `stocks` SET `sharesInExchange` = `sharesInExchange` - $numToBuy WHERE `stockId` = '{$shareId}';
UPDATE `users_data` SET `value` = `value` + $numToBuy WHERE `userId` = '{$userId}' AND `key` = '{$shareId}';
UPDATE `users_data` SET `value` = `value` - $amount WHERE `userId` = '{$userId}' AND `key` = 'cashInHand';
COMMIT;
QUERY;
        $failed = runQueries($query);
        if($failed) return array("error" => "Unknown database error, we looking into it, try after sometime");
        if($message=="") {
            $message = "Bought $number shares of {$row['name']}";
        }
        return array("message" => $message);
    } else {
        return array("error" => "Stock not found in database");
    }
}

?>
