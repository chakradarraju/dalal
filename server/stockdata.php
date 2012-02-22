<?php

require_once("users.php");
require_once("stock.php");

if(getLoggedInUserId()==-1) { //If not logged in
    $error['error'] = "Your session expired<br />Please login again";
    die(json_encode($error));
}

$stockId = "All";
if(isset($_GET['stockId'])) {
    $stockId = mysql_real_escape_string($_GET['stockId']);
    if($stockId!="Index"&&!stockIdExists($stockId)) {
        $error['error'] = "Invalid request";
        die(json_encode($error));
    }
}

$graphSpan = GRAPH_SPAN;
if($stockId=="Index") {
    $result = mysql_query("SELECT `time`, `value` FROM `misc_data` WHERE `key` = 'index' AND `time` > NOW() - INTERVAL {$graphSpan}");
    while($row=mysql_fetch_assoc($result)) {
        $values[$row['time']] = $row['value'];
    }
    echo json_encode($values);
} else if($stockId=="All") {
    $result = mysql_query("SELECT * FROM `stocks_data` WHERE `key` = 'graph_point' AND `time` > NOW() - INTERVAL {$graphSpan}");
    while($row=mysql_fetch_assoc($result)) {
        $values[$row['stockId']][$row['time']] = $row['value'];
    }
    $result = mysql_query("SELECT * FROM `stocks`");
    while($row=mysql_fetch_assoc($result)) {
        $rowStockId = $row['stockId'];
        $stocks[$rowStockId] = $row;
        if($values[$rowStockId]) $graph = $values[$rowStockId];
        else $graph = array();
        $stocks[$rowStockId]['graph'] = $graph;
    }
    echo json_encode($stocks);
} else {
    $result = mysql_query("SELECT `time`, `value` FROM `stocks_data` WHERE `stockId` = '{$stockId}' AND `key` = 'graph_point' AND `time` > NOW() - INTERVAL {$graphSpan}");
    while($row=mysql_fetch_assoc($result)) {
        $values[$row['time']] = $row['value'];
    }
    $result = mysql_query("SELECT * FROM `stocks` WHERE `stockId` = '{$stockId}'");
    if($row=mysql_fetch_assoc($result)) {
        $stock = $row;
        $stock['graph'] = $values;
    }
/*    $result = mysql_query("SELECT NOW() AS 'now' FROM `users`");
    $row = mysql_fetch_assoc($result);
    $stock['graph'][$row['now']] = */
    echo json_encode($stock);
}

?>
