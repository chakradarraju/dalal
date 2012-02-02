<?php
$mypost = $_POST;
$_POST = array();
require_once("users.php");
echo "Logged in as: " . getLoggedInUserId() . "<br/>";
if(isset($mypost['mortgage'])) {
    $shareId = $mypost['shareId'];
    $number = $mypost['number'];
    $value = $mypost['value'];
    require_once("bank.php");
    $return = mortgage($shareId,$number,$value);
    echo json_encode($return);
}
if(isset($mypost['recover'])) {
    $mortgageId = $mypost['mortgageId'];
    require_once("bank.php");
    $return = recover($mortgageId);
    echo json_encode($return);
}
if(isset($mypost['list'])) {
    require_once("bank.php");
    echo json_encode(getUserMortgages());
}
?>
<html>
<head>
<title></title>
</head>
<body>
<form action="./mortgage.php" method=POST>
shareId:<input name=shareId /><br/>
number:<input name=number /><br/>
value:<input name=value /><br/>
<input type=submit name=mortgage value=mortgage />
</form>
<form action="./mortgage.php" method=POST>
mortgageId:<input name=mortgageId /><br/>
<input type=submit name=recover value=recover />
</form>
<form action="./mortgage.php" method=POST>
<input type=submit name=list value=list />
</form>
</body>
</html>
