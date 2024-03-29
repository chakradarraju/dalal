<?php
require_once("server/users.php");
$userId = getLoggedInUserId();
if($userId==-1) {
    header("Location: ./login.php");
}
?>
<!-- Wartortle Template by Dinesh Prasanth <mail2dineshnow@gmail.com> -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>Dalal Street 2012</title>
<script type="text/javascript" src="scripts/jquery.min.js"></script>
<script type="text/javascript" src="scripts/main.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js"></script>
<script type="text/javascript" src="scripts/jquery-ui-1.8.17.custom.min.js"></script>
<link rel="shortcut icon" href="images/favicon.ico" >
<link href="styles/main.css" rel="stylesheet"></link>
<link href="styles/start/jquery-ui-1.8.17.custom.css" rel="stylesheet"></link>
</head>
<body>
<div id='dialog' style='display:none' title='Your profile'>
<?php echo getProfileForm($userId); ?>
</div>
<?php
session_start();
$alert = "";
if(isset($_SESSION['message'])) {
    $alert = $_SESSION['message'];
    unset($_SESSION['message']);
}
if(isset($_SESSION['error'])) {
    $alert = $_SESSION['error'];
    unset($_SESSION['error']);
}
if($alert!="") {
    $js = "
<script type='text/javascript'>
$(function() {
    alert('{$alert}');
});
</script>
";
    echo $js;
}
if(!isUserProfileFilled($userId)) {
    $js = "
<script type='text/javascript'>
$(function() {
    $('#dialog').dialog();
});
</script>
";
    echo $js;
}
?>
<div id="header">
<div id="header_title"></div>
<div id="header_buttons" class="button_font">
<div id="help"><a style="color:#4E78AC;text-decoration:none" href='./server/help.php'>Help</a></div>
<div id="logout"><a style="color:#4E78AC;text-decoration:none" href='./server/logout.php'>Logout</a></div>
<div id="editprofile"><a style="color:#4E78AC;text-decoration:none" onclick="$('#dialog').dialog()">Edit Profile</a></div>
</div>
</div>
<div id="outer_container">
<div id="menu_icons">
<div id="updates"><div id="updates_inner">
<?php
	$file = fopen("./supports/updates.txt", "r") or exit("Unable to open file!");
	$i=1;
	while(!feof($file)){
	echo '<label id="'.$i.'">'.'['.$i.'] '.fgets($file).'</label>';
	$i++;
	}
	fclose($file);
	?>
</div><div id="menu_right"></div><div id="menu_left"></div></div>
<div id="icons_outer">
<div id="icon_home"></div>
<div id="icon_market"></div>
<div id="icon_trade"></div>
<div id="icon_ranking"></div>
<div id="icon_bank"></div>
<div id="icon_forum"></div>
</div>
</div>
<div id="main_container">
<div id="homedata" class="datas" >
<div id="content_header">
<div id="content_header_inner">HOME</div>
</div>
<label>Welcome</label>
<div id="home_stock">
<table id="stock_table">
<tbody>
<tr title="sort"><td>Company Name</td><td>Shares</td></tr>
<tr><td>Company ABC</td><td>23000</td></tr>
</tbody>
</table>
</div>
<div id="cashinhand" class="valuebox">000</div>
<div id="networth" class="valuebox">000</div>
<div id="yourrank" class="valuebox">00</div>

</div>


<div class="datas" id="marketdata">
<div id="content_header">
<div id="content_header_inner">MARKET</div>
</div>
<div id="market_bg"><label><b>Click a company on the list . . .</b></label><div id="company_details">
<table>
<tr>
<td>Current Market Price:<span class="market_price"></span></td>
<td>Exchange Price:<span class="exchange_price"></span></td>
</tr>
<tr>
<td>Shares Issued:<span class="shares_issued"></span></td>
<td>Shares In Exchange:<span class="in_exchange"></span></td>
</tr>
<tr>
<td>Last Traded Value:<span class="last_trade"></span></td>
<td></td>
</tr>
<tr>
<td>Day High Value:<span class="day_high"></span></td>
<td>Day Low Value:<span class="day_low"></span></td>
</tr>
</table>
</div></div>
<input type="text" class="valuebox" id="in_buyshares">
<br />
<input type="button" class="but" id="but_buy">
</div>
<div class="datas" id="tradedata">
<div id="content_header">
<div id="content_header_inner">TRADE POINT</div>
</div>
<div id="trade_stock">
<table id="stock_table">
<tbody>
<tr title="sort"><td>Company Name</td><td>Shares</td><td>Request</td><td>Quote</td><td>(cancel?)</td></tr>
</tbody>
</table>
</div>
<div id="trade_company">Selected Company : <span id="selectedCompany1"></span> </div>
<input type="text" class="valuebox" id="in_setquote"><br />
<input type="text" class="valuebox" id="in_setnumber"><br />
<input type="button" class="but" id="but_buy">
<input type="button" class="but" id="but_sell">
<!--<input type="button" class="but" id="but_sell">
<input type="button" class="but" id="but_mortgage">-->
</div>
<div class="datas" id="rankdata">
<div id="ranktable_outer">
<table id="rank_table">
<tbody>
<tr><td>Rank</td><td>Names</td><td>Net Worth</td></tr>
</tbody>
</table>
</div>
</div>
<div class="datas" id="bankdata">
<div id="content_header">
<div id="content_header_inner">BANK</div>
</div>
<div id="bank_stock">
<table id="stock_table">
<tbody>
<tr title="sort"><td>Company Name</td><td>Shares</td><td>Net value</td><td>(take back?)</td></tr>
</tbody>
</table>
</div>
<div id="mortgage_company">Selected Company : <span id="selectedCompany2"></span> <label id="mortgage_max"></label></div>
<input type="text" class="valuebox" id="in_setnumber"><br />
<input type="text" class="valuebox" id="in_quote_value"><br />
<input type="button" class="but" id="but_mortgage">
</div>
</div>
<div id="list_container">
<div id="list_header"></div>
<div id="list_inner">
<table id="list_table">
<tbody>
<tr title="sort"><td>Company Name</td><td>Share Rate</td></tr>
<tr><td>Company company company</td><td>23000</td></tr>
<tr><td>Google</td><td>1200</td></tr>
<tr><td>Microsoft</td><td>870</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
</tbody>
</table>
</div>
</div>
<div id="graph_container">
<div id="statistics_header">
<div id="stat_header_inner">STATISTICS</div>
<div id="yuigraph-container">

</div>
</div>
</div>
</div>
<div id="footer"></div>
</body>
</html>
