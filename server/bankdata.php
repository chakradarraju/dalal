<?php
require_once("bank.php");

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

$list = getUserMortgages($userId);
die(json_encode($list));

?>
