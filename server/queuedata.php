<?php
require_once("users.php");

$userId = getLoggedInUserId();
if($userId==-1) {
    $error['error'] = "Your session expired<br />Please login again";
    die(json_encode($error));
}

if(isset($_POST['cancelOrder'])) {
    $type = mysql_real_escape_string($_POST['type']);
    $orderId = mysql_real_escape_string($_POST['orderId']);
    $supportedType = array("buy","sell");
    if(!in_array($type,$supportedType)) {
        $error['error'] = "Invalid request";
        die(json_encode($error));
    }
    $result = mysql_query("SELECT `userId` FROM `{$type}` WHERE `{$type}Id` = '{$orderId}'");
    if($row = mysql_fetch_assoc($result)) {
        if($row['userId']==$userId) {
            $result = mysql_query("DELETE FROM `{$type}` WHERE `{$type}Id` = '{$orderId}'");
            if(!$result) die(json_encode(array("error" => "Database Error, we're working on it, will be fixed soon")));
            else die(json_encode(array("message" => "Successfully deleted")));
        } else {
            die(json_encode(array("message" => "Seems something went wrong")));
        }
    } else {
        die(json_encode(array("message" => "The {$type} request was not found in database")));
    }
}

$result = mysql_query("SELECT * FROM `buy` WHERE `userId` = '{$userId}'");
$return = array();
while($row = mysql_fetch_assoc($result)) {
    $row['type'] = "buy";
    $return[] = $row;
}
$result = mysql_query("SELECT * FROM `sell` WHERE `userId` = '{$userId}'");
while($row = mysql_fetch_assoc($result)) {
    $row['type'] = "sell";
    $return[] = $row;
}

echo json_encode($return);
?>
