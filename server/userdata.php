<?php

require_once("users.php");
require_once("stock.php");

$userId = getLoggedInUserId();
if($userId==-1) { //If not logged in
    $error['error'] = "Your session expired<br />Please login again";
    die(json_encode($error));
}

$getDetail = "portfolio";
if(isset($_GET['getDetail'])) {
    $getDetail = mysql_real_escape_string($_GET['getDetail']);
    $supportedOptions = array("portfolio","ranklist");
    if(!in_array($getDetail,$supportedOptions)) {
        $error['error'] = "Invalid request";
        die(json_encode($error));
    }
}

$graphSpan = GRAPH_SPAN;
if($getDetail=="portfolio") {
    $result = mysql_query("SELECT `time`, `key`, `value` FROM `users_data` WHERE `userId` = {$userId}");
    $hiddenDetails = getUserHiddenDetails();
    $userDetail['userId'] = $userId;
    while($row=mysql_fetch_assoc($result)) {
        if(!in_array($row['key'],$hiddenDetails)) {
            if($row['key']=="graph_point") {
                $userDetail['graph'][$row['time']] = $row['value'];
            } else if(stockIdExists($row['key'])) {
                if($row['value']>0) $userDetail['stocks'][$row['key']] = $row['value'];
            } else {
                $userDetail[$row['key']] = $row['value'];
            }
        }
    }
    echo json_encode($userDetail);
} else if($getDetail=="ranklist") {
    $interval = UPDATE_INTERVAL;
    $result = mysql_query("SELECT `value` FROM `misc_data` WHERE `key` = 'ranklist' AND `time` > NOW() - INTERVAL {$interval}");
    if($row=mysql_fetch_assoc($result)) {
        echo $row['value'];
    } else {
        $result = mysql_query("SELECT u.`userId`, SUM(u.value*s.marketValue) AS 'holdings' FROM `users_data` AS u LEFT JOIN `stocks` AS s ON u.key = s.stockId GROUP BY u.`userId`");
        while($row = mysql_fetch_assoc($result)) {
            $holdings = 0;
            if($row['holdings']!==NULL) $holdings = $row['holdings'];
            $users[$row['userId']]['holdings'] = $holdings;
        }
        $result = mysql_query("SELECT `userId`, `key`, `value` FROM `users_data` WHERE `key` = 'cashInHand' OR `key` = 'Display_Name'");
        while($row = mysql_fetch_assoc($result)) {
            $users[$row['userId']][$row['key']] = $row['value'];
        }
        $ranklist = array();
        foreach($users as $userId => $user) {
            $thisUser = $user;
            $thisUser['userId'] = $userId;
            $thisUser['totalWorth'] = $user['cashInHand']+$user['holdings'];
            $ranklist[] = $thisUser;
        }
        usort($ranklist, function($a,$b) {
            $aWorth = $a['totalWorth'];
            $bWorth = $b['totalWorth'];
            return $aWorth==$bWorth?0:$aWorth>$bWorth?-1:1;
        });
        $currentRank = 1;
        foreach($ranklist as $key => $value) {
            $ranklist[$key]['rank'] = $currentRank++;
        }
        $ranklistText = json_encode($ranklist);
        foreach($ranklist as $rank => $user) {
            $query =<<<QUERY
INSERT INTO `users_data`
    SELECT '{$user['userId']}', NOW(), 'graph_point', ROUND({$user['totalWorth']},2) FROM `users`
    WHERE NOT EXISTS (
        SELECT * FROM `users_data` WHERE `userId` = '{$user['userId']}' AND `key` = 'graph_point' AND `time` > NOW() - INTERVAL {$interval}
    ) LIMIT 1;
QUERY;
            mysql_query($query);
        }
        mysql_query("INSERT INTO `misc_data` VALUES(NULL,'ranklist','{$ranklistText}')");
        echo $ranklistText;
    }
}
?>
