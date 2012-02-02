<?php
if(isset($_POST['tradetemp'])) {
    echo "Came in<br/>";
    echo json_encode($_POST);
    $shareId = $_POST['shareId'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    if(!Is_Numeric($shareId)) die("Share Id not numeric");
    if(!Is_Numeric($qty)) die("Qty is not numeric");
    if(!Is_Numeric($price)) die("price is not numeric");
    echo "done checking<br/>";
    require_once("exchange.php");
    echo "done exchange<br/>";
    if($_POST['tradetemp']=='buy') {
        echo "buy";
        $result = buy($shareId,$qty,$price);
    } else if($_POST['tradetemp']=='sell') {
        echo "sell";
        $result = sell($shareId,$qty,$price);
    } else {
        echo "Buy from exchange<br>";
        $result = buyFromExchange($shareId,$qty);
    }
    echo "<br/>result: " . json_encode($result);
}
?>
<html>
<head>
<title></title>
</head>

<body>
<form action="./trade.php" method=POST>
<?php
require_once("users.php");
echo "Logged in with userid " . getLoggedInUserId() . "<br/>";
?>
ShareId:<input type=text name=shareId /><br/>
Quantity:<input type=text name=qty /><br/>
Price:<input type=text name=price /><br/>
<select name=tradetemp >
<option value=buy>Buy</option>
<option value=sell>Sell</option>
<option value=buyFromExchange>Buy From Exchange</option>
</select>
<input type=submit value=Trade />
</form>
</body>
</html>
