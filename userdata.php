<?php

require_once("users.php");
require_once("stock.php");

$userId = getLoggedInUserId();
if($userId==-1) { //If not logged in
    $error['error'] = "Your session expired<br />Please login again";
    die(json_encode($error));
}

$getDetail = "All";
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
    while($row=mysql_fetch_assoc($result)) {
        if(!in_array($row['key'],$hiddenDetails)) {
            if($row['key']=="graph_point") {
                $userDetail['graph'][$row['time']] = $row['value'];
            } else if(stockIdExists($row['key'])) {
                $userDetail['stocks'][$row['key']] = $row['value'];
            } else {
                $userDetail[$row['key']] = $row['value'];
            }
        }
    }
} else if($getDetail=="ranklist") {
    $interval = UPDATE_INTERVAL;
    $result = mysql_query("SELECT `value` FROM `misc_data` WHERE `key` = 'ranklist' AND `time` > NOW() - INTERVAL {$interval}");
    if($row=mysql_fetch_assoc($result)) {
        echo $row['value'];
    } else {
        $result = mysql_query("SELECT u.`userId`, SUM(u.value*(s.p1+s.p2+s.p3+s.p4+s.p5)/5) AS 'holdings' FROM `users_data` u OUTER JOIN `stocks` s ON u.key == s.stockId GROUP BY `userId`");
        while($row = mysql_fetch_assoc($result)) {
            $users[$row['userId']]['holdings'] = $row['holdings'];
        }
        $result = mysql_query("SELECT `userId`, `value` FROM `users_data` WHERE `key` = 'cashInHand'");
        while($row = mysql_fetch_assoc($result)) {
            $users[$row['userId']]['cashInHand'] = $row['value'];
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
        $ranklistText = json_encode($ranklist);
        mysql_query("INSERT INTO `misc_data` VALUES(NULL,'ranklist','{$ranklistText}')");
        echo $ranklistText;
    }
}
?>
