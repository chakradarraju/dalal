<?php

/**
 * function getMarketValue(stockId)
 * returns the moving average of the given stock(stockId)
 * moving average is the average of previous 5 traded values of the stock
 **/
function getMarketValue($stockId) {
    $query = "SELECT `marketValue` FROM `stocks` WHERE `stockId` = '{$stockId}'";
    $result = mysql_query($query);
    if(!$result) return -1;
    if($row = mysql_fetch_assoc($result)) {
        return $row['marketValue'];
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
