<?php

/**
 * function buy(stockId,num,value)
 * Buy 'num' number of stock 'stockId' based on previous sell orders <= 'value'
 * return status of the buy request as string
 **/
function buy($stockId,$num,$value) {
	require_once("users.php");
	$Cnum = $num;
	$buyId = getLoggedInUserId();
	$movingAverage = getMovingAverage($stockId);
	$query = "SELECT `userId`,`num`,`value` FROM `sell` WHERE `stockId` = '{$stockId}' ORDER BY `value` ASC";
	$result = mysql_query($query);
	while($num>0&&$row=mysql_fetch_assoc($result)) {
		if($row['value']>1.1*$movingAverage||$row['value']>$value) break;
		$cur_num = small($num,$row['num']);
		$cur_val = ($value+$row['value'])/2;
		$tradeResult = trade($row['userId'],$buyId,$stockId,$cur_num,$cur_value);
		if($tradeResult==1) $num -= $cur_num;
	}
	if($value>=1.1*$movingAverage&&$num>0) {
		$cur_value = ($value+1.1*$movingAverage)/2;
		$tradeResult = trade(-1402,$buyId,$stockId,$num,$cur_value);
		if($tradeResult==1) $num = 0;
		else return "Bought ".$Cnum-$num;
	}
	if($num>0) {
		$insQuery = "INSERT INTO `buy` VALUES('{$buyId}','{$stockId}','{$num}','{$value}')";
		$insResult = mysql_query($insQuery);
		$traded = $Cnum-$num;
		if($insResult) return "Bought {$traded}, couldn't place more orders, try again later";
		return "Bought {$traded}, and placed order for {$num} at the rate of {$value}";
	}
	return "Bought {$Cnum}";
}

/**
 * function sell(stockId,num,value)
 * Sell 'num' number of stock 'stockId' based on previous buy orders >= 'value'
 * return status of the buy request as string
 **/
function sell($stockId,$num,$value) {
	require_once("users.php");
	$Cnum = $num;
	$sellId = getLoggedInUserId();
	$movingAverage = getMovingAverage($stockId);
	if($value<0.9*$movingAverage) {
		$cur_value = ($value+0.9*$movingAverage)/2;
		$tradeResult = trade($sellId,-1402,$stockId,$num,$cur_value);
		if($tradeResult==1) $num = 0;
		else return "Error processing order";
	}
	$query = "SELECT `userId`,`num`,`value` FROM `buy` WHERE `stockId` = '{$stockId}' ORDER BY `value` DESC";
	$result = mysql_query($query);
	while($num>0&&$row=mysql_fetch_assoc($result)) {
		if($row['value']<$value) break;
		$cur_num = small($num,$row['num']);
		$cur_val = ($value+$row['value'])/2;
		$tradeResult = trade($sellId,$row['userId'],$stockId,$cur_num,$cur_value);
		if($tradeResult==1) $num -= $cur_num;
	}
	if($num>0) {
		$insQuery = "INSERT INTO `sell` VALUES('{$buyId}','{$stockId}','{$num}','{$value}')";
		$insResult = mysql_query($insQuery);
		$traded = $Cnum-$num;
		if($insResult) return "Sold {$traded}, couldn't place more orders, try again later";
		return "Sold {$traded}, and placed order for {$num} at the rate of {$value}";
	}
	return "Sold {$Cnum}";
}

/**
 * function trade(fromId,toId,stockId,num,value)
 * trades 'num' shares of type 'stockId' from 'fromId' user to 'toId' user
 * at the rate of 'value' per share
 * return 1 on success
 * return -1 in case of any error
 **/
function trade($fromId,$toId,$stockId,$num,$value) {
	require_once("users.php");
	require_once("stock.php");
	if(!userIdExists($fromId)||!userIdExists($toId)||!stockIdExists($stockId)) return "Invalid inputs";
//	$fromId = mysql_real_escape_string($fromId);
//	$toId = mysql_real_escape_string($toId);
//	$query = "trade({$fromId},{$toId},{$stockId},{$num},{$value})";
	$fromCash = cashInHand($fromId);
	$toCash = cashInHand($toId);
	$tradeValue = $value*$num;
	$fromNum = sharesInHand($fromId,$stockId);
	$toNum = sharesInHand($toId,$stockId);
	if($fromCash==-1||$toCash==-1||$fromNum==-1||$toNum==-1||$toCash<$tradeValue||$fromNum<$num) return -1;
	$interval = UPDATE_INTERVAL;
	$fromCash += $tradeValue;
	$toCash -= $tradeValue;
	$fromNum -= $num;
	$toNum += $num;
	$query = <<<QUERY
START TRANSACTION;
UPDATE `users` SET `cashInHand` = '{$fromCash}' WHERE `userId` = '{$fromId}';
UPDATE `users` SET `cashInHand` = '{$toCash}' WHERE `userId` = '{$toId}';
UPDATE `holding` SET `num` = '{$fromNum}' WHERE `userId` = '{$fromId}' AND `stockId` = '{$stockId}';
UPDATE `holding` SET `num` = '{$toNum}' WHERE `userId` = '{$toId}' AND `stockId` = '{$stockId}';
UPDATE `stock` SET `p1` = `p2`, `p2` = `p3`, `p3` = `p4`, `p4` = `p5`, `p5` = '{$tradeValue}' WHERE `stockId` = '{$stockId}';
INSERT INTO `log` VALUES('{$fromId}','{$toId}','{$stockId}','{$num}','{$value}');
INSERT INTO `values`
	SELECT 'stock_{$stockId}', NOW(), '{$value}' FROM `values`
	WHERE NOT EXISTS (
		SELECT * FROM `values` WHERE `key` = 'stock_{$stockId}' AND `time` > NOW() - INTERVAL {$interval}
	)
INSERT INTO `values`
    SELECT 'index', NOW(), SUM((`p1`+`p2`+`p3`+`p4`+`p5`)/5*`factor`) FROM `stock`
    WHERE NOT EXISTS (
        SELECT * FROM `values` WHERE `key` = 'index' AND `time` > NOW() - INTERVAL {$interval}
    )
COMMIT;
QUERY;
	if(!mysql_query($query)) return -1;
	return 1;
}

/**
 * function sharesInHand(userId,stockId)
 * uses "holding" table to return the number of stocks held by given user
 * return -1 in case of any error
 **/
function sharesInHand($userId,$stockId) {
	$query = "SELECT `num` FROM `holding` WHERE `userId` = '{$userId}' AND `stockId` = '{$stockId}'";
	$result = mysql_query($query);
	if(!$result) return -1;
	if(mysql_num_rows($result)==0) {
		$insQuery = "INSERT INTO `holding` VALUES('{$userId}','{$stockId}','0')";
		if(!mysql_query($insQuery)) return -1;
		return 0;
	}
	$row = mysql_fetch_assoc($result);
	return $row['num'];
}

/**
 * function getMovingAverage(stockId)
 * returns the moving average of the given stock(stockId)
 * moving average is the average of previous 5 traded values of the stock
 **/
function getMovingAverage($stockId) {
	$query = "SELECT * FROM `stock` WHERE `stockId` = '{$stockId}'";
	$result = mysql_query($query);
	if(!$result) return -1;
	if($row = mysql_fetch_assoc($result)) {
		return ($row['p1']+$row['p2']+$row['p3']+$row['p4']+$row['p5'])/5;
	}
	return -1;
}

?>
