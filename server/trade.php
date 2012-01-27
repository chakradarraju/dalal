<?php
if(isset($_POST['trade'])) {
    echo "Came in<br/>";
    echo json_encode($_POST);
    $shareId = $_POST['shareId'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    if(!Is_Numeric($shareId)) die("Share Id not numeric");
    if(!Is_Numeric($qty)) die("Qty is not numeric");
    if(!Is_Numeric($price)) die("price is not numeric");
    require_once("exchange.php");
    if($_POST['trade']=='buy') {
        $result = buy($shareId,$qty,$price);
    } else {
        $result = sell($shareId,$qty,$price);
    }
    echo "<br/>result: " . $result;
}
?>
<html>
<head>
<title></title>
</head>

<body>
<form action="./exchange.php" method=POST>
<?php
require_once("users.php");
echo "Logged in with userid " . getLoggedInUserId() . "<br/>";
?>
ShareId:<input type=text name=shareId /><br/>
Quantity:<input type=text name=number /><br/>
Price:<input type=text name=rate /><br/>
<select name=trade >
<option value=buy>Buy</option>
<option value=sell>Sell</option>
</select>
<input type=submit value=Trade />
</form>
</body>
</html>
