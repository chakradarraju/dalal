<?php

/**
 * function getMovingAverage(stockId)
 * returns the moving average of the given stock(stockId)
 * moving average is the average of previous 5 traded values of the stock
 **/
function getMovingAverage($stockId) {
	$query = "SELECT * FROM `stocks` WHERE `stockId` = '{$stockId}'";
	$result = mysql_query($query);
	if(!$result) return -1;
	if($row = mysql_fetch_assoc($result)) {
		return ($row['p1']+$row['p2']+$row['p3']+$row['p4']+$row['p5'])/5;
	}
	return -1;
}

function stockIdExists($stockId) {
    $result = mysql_query("SELECT * FROM `stocks` WHERE `stockId` = '{$stockId}'");
    if($result===NULL) return false;
    if(mysql_num_rows($result)>0) return true;
    else return false;
}

?>
