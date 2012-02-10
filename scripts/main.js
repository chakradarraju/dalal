$(function(){


$('#list_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
$('#stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
up=1;
upsize = $('#updates_inner > label').size()+1;
function update_next(){
$('#'+up).delay(3000).animate({'opacity':'0'},{duration:'5',complete:function(){$('#'+up).css({'display':'none'});up++;if(up==upsize){up=1;}$('#'+up).css({'display':'block'});$('#'+up).animate({'opacity':'1'},{duration:'5',complete:function(){update_next();}})}})
}
update_next();
$('#menu_right').click(function(){
$('#updates_inner > label').stop();
$('#'+up).stop().css({'display':'none'});
up++;
if(up==upsize){up=1;}
$('#'+up).css({'display':'block','opacity':'1'});
});
$('#menu_left').click(function(){
$('#'+up).stop().css({'display':'none'});
up--;
if(up==0){up=upsize-1;}
$('#'+up).css({'display':'block','opacity':'1'});
});

function sethome_stock(stockdata1,userdata){
stockdata = jQuery.parseJSON(stockdata1);
stock = userdata["stocks"];
var prop = 0;
stock_tbody="<tr title=\"sort\"><td>Company Name</td><td>Shares</td></tr>";
company_tbody = "<tr title=\"sort\"><td>Company Name</td><td>Share Rate</td></tr>";
for(var x in stockdata){
company_tbody+="<tr><td>"+stockdata[x]["name"]+"</td><td>"+stockdata[x]["marketValue"]+"</td></tr>";
}
for(var x in stock){
prop+=parseFloat(stockdata[x]["marketValue"])*parseFloat(stock[x]);
stock_tbody+="<tr><td>"+stockdata[x]["name"]+"</td><td>"+stock[x]+"</td></tr>";
}
networth=parseFloat(userdata['cashInHand'])+prop;
$('#networth').html(networth.toFixed(2));
$("#home_stock > #stock_table > tbody").html(stock_tbody);
$("#list_table > tbody").html(company_tbody);
$('#list_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
$('#home_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
}


function setbank_stock(stockdata1,banklist1){
banklist = jQuery.parseJSON(banklist1);
stockdata = jQuery.parseJSON(stockdata1);
stock_tbody="<tr title=\"sort\"><td>Company Name</td><td>Shares</td><td>Net value</td><td>(take back?)</td></tr>";
company_tbody = "<tr title=\"sort\"><td>Company Name</td><td>Share Rate</td></tr>";
mortgage_company = "";
for(var x in stockdata){
company_tbody+="<tr><td>"+stockdata[x]["name"]+"</td><td>"+stockdata[x]["marketValue"]+"</td></tr>";
mortgage_company+="<option value=\""+stockdata[x]["stockId"]+"\">"+stockdata[x]["name"]+"</option>";
}

for(var x in banklist){
stock_tbody+="<tr><td class=\""+banklist[x]["mortgageId"]+"\">"+stockdata[banklist[x]["stockId"]]["name"]+"</td><td>"+banklist[x]["number"]+"</td><td>"+banklist[x]["loanValue"]+"</td><td><div class=\"bank_closer\"></div></td></tr>";
}
$("#bank_stock > #stock_table > tbody").html(stock_tbody);
$("#list_table > tbody").html(company_tbody);
$("#mortgage_company > select").html(mortgage_company);
$('#list_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
$('#bank_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
$('.bank_closer').click(function(){
comp_tr=$(this).parent().parent();
comp_td=$(comp_tr).find('td');
$.post("server/bankdata.php", { recover: "", mortgageId: $(comp_td[0]).attr('class')},
function(data) {
	alert(data);
	$(comp_tr).remove();
	$('#bank_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
	$('#bank_stock > #stock_table > tbody > tr:even').css({'background-color':''});
});
});
}

function settrade_stock(stockdata1,queuedata1){
stockdata = jQuery.parseJSON(stockdata1);
queuedata = jQuery.parseJSON(queuedata1);
var prop = 0;
trade_company = "";
stock_tbody="<tr title=\"sort\"><td>Company Name</td><td>Shares</td><td>Quote</td><td>Request</td><td>(cancel?)</td></tr>";
company_tbody = "<tr title=\"sort\"><td>Company Name</td><td>Share Rate</td></tr>";
for(var x in queuedata){
stock_tbody+="<tr><td class=\""+queuedata[x][queuedata[x]["type"]+"Id"]+"\">"+stockdata[queuedata[x]["stockId"]]["name"]+"</td><td>"+queuedata[x]["num"]+"</td><td>"+queuedata[x]["value"]+"</td><td>"+queuedata[x]["type"]+"</td><td><div class=\"trade_closer\"></div></td></tr>";
}
for(var x in stockdata){
company_tbody+="<tr><td>"+stockdata[x]["name"]+"</td><td>"+stockdata[x]["marketValue"]+"</td></tr>";
trade_company+="<option value=\""+stockdata[x]["stockId"]+"\">"+stockdata[x]["name"]+"</option>";
}

$("#trade_stock > #stock_table > tbody").html(stock_tbody);
$('.trade_closer').click(function(){
comp_tr=$(this).parent().parent();
comp_td=$(comp_tr).find('td');
$.post("server/trade.php", { cancelOrder: "", type: $(comp_td[3]).html(), orderId: $(comp_td[0]).attr('class')},
function(data) {
	alert(data);
	$(comp_tr).remove();
	$('#trade_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
	$('#trade_stock > #stock_table > tbody > tr:even').css({'background-color':''});
});
});
$("#list_table > tbody").html(company_tbody);
$("#trade_company > select").html(trade_company);
$('#list_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
$('#trade_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
}

function setuser_rank(userid,rankdata1){
rankdata = jQuery.parseJSON(rankdata1);
userid = parseFloat(userid);
for(x in rankdata){
if(rankdata[x]["userId"]==userid)
	$('#yourrank').html(rankdata[x]["rank"]);
}
}
function yuigraphs(yuigraph,m,n)
		{
				n1=parseInt(n)+500
				YUI().use('charts',function(Y){
				 var styleDef = {
        axes:{
            category:{
                label:{
                    rotation:-45,
                    color: "#000"
                }
			},
			values:{
			type:"numeric",
			label:{
				color: "#000"
			}
			}
				},
		series:{
			series1:{
			marker:{
				
				 fill:{
                            color:"#FFC125"
                        },
                        border:{
                            color:"#999"
                        },
                        over:{
                            fill:{
                                color:"#FFFF00"
                            },
                            border:{
                                color:"#FFA500"
                            },
                            width: 12,
                            height: 12
                        }
                    },
                    line:{
                        color:"#FF3030"
                    },			
			}
			}
		};
			var myTooltip = {
            styles: { 
                backgroundColor: "#333",
                color: "#eee",
                borderColor: "#fff",
                textAlign: "center"
            },
            markerLabelFunction: function(categoryItem, valueItem, itemIndex, series, seriesIndex)
            {
                var msg = "<span>" + "value" + " at " + 
                categoryItem.axis.get("labelFunction").apply(this, [categoryItem.value, categoryItem.axis.get("labelFormat")]) + 
                "</span><br/><div>" + valueItem.axis.get("labelFunction").apply(this, [valueItem.value, {prefix:"$", decimalPlaces:2}]) + "</div>";
             
			 return msg; 
            }
        };
				var mychart= new Y.Chart({
				dataProvider: yuigraph,
				styles: styleDef,
				horizontalGridlines:true, 
                verticalGridlines:true,
				tooltip: myTooltip,
//				categoryKey:"date", 
//				categoryType:"time",
				render: "#graph_container"
				})
	//			mytimeaxis=mychart.getAxisByKey("category");
		//		mytimeaxis.set("labelFunctionScope",mychart);
	
				mynumericaxis = mychart.getAxisByKey("values");
				mynumericaxis.set("labelFunctionScope", mychart);
				mynumericaxis.set("minimum",m-500);
				mynumericaxis.set("maximum",n1);
				//mynumericaxis.set("categoryAxisName","value");
				mynumericaxis._drawAxis();
				;	
				});
		}
		function getjson()
		{	var container=document.getElementById("graph_container");
			container.innerHTML='';
		    $.getJSON("server/userdata.php", function(json){
			console.log(json.graph);
			var yuigraph = JSON.stringify(json.graph);
			var j = 0;
			 var arr = [];
			for (var i=0;i<2;i++) {
			arr[i] = [];
  }
			k1=0
			var data=$.parseJSON(yuigraph);
			$.each(data, function(key, value) {
				var key1=key.substring(11,key.length)
				/*var key2=key1.substring(0,2)
				var key3=key1.substring(3,5);
				var key4=key1.substring(6,key1.length);
				var key5=parseInt(key2)*3600+parseInt(key3)*60+parseInt(key4)*/
				arr[0][j]=key1;
				arr[1][j]=value;
				if(k1==0)
				{
					m=value
					n=value
					k1=1
				}
				else
				{
				if(value < m)
					m=value
				if(value > n)
					n=value
				}
				j=j+1;
			});
		//		alert(yuigraph);
			yuigraphs(arr,m,n);	
		});
		}
function sethome_user(userdata1){
userdata = jQuery.parseJSON(userdata1);
$('#homedata > label').html("Welcome home "+userdata['Display_Name']+" !!");
$('#cashinhand').html(userdata['cashInHand']);
$.ajax({
  url: 'server/userdata.php?getDetail=ranklist',
  success: function(rankdata) {
    setuser_rank(userdata['userId'],rankdata);
  }
 });
$.ajax({
  url: 'server/stockdata.php',
  success: function(stockdata) {
    sethome_stock(stockdata,userdata);
  }
 });
}

function refresh_tradestock(stockdata1,queuedata1){
$('#tradedata > #in_setquote').attr('value','');
$('#tradedata > #in_setnumber').attr('value','');
stockdata = jQuery.parseJSON(stockdata1);
queuedata = jQuery.parseJSON(queuedata1);
var prop = 0;
trade_company = "";
stock_tbody="<tr title=\"sort\"><td>Company Name</td><td>Shares</td><td>Quote</td><td>Request</td><td>(cancel?)</td></tr>";
company_tbody = "<tr title=\"sort\"><td>Company Name</td><td>Share Rate</td></tr>";
for(var x in queuedata){
stock_tbody+="<tr><td class=\""+queuedata[x][queuedata[x]["type"]+"Id"]+"\">"+stockdata[queuedata[x]["stockId"]]["name"]+"</td><td>"+queuedata[x]["num"]+"</td><td>"+queuedata[x]["value"]+"</td><td>"+queuedata[x]["type"]+"</td><td><div class=\"trade_closer\"></div></td></tr>";
}
$("#trade_stock > #stock_table > tbody").html(stock_tbody);
$('.trade_closer').click(function(){
comp_tr=$(this).parent().parent();
comp_td=$(comp_tr).find('td');
$.post("server/trade.php", { cancelOrder: "", type: $(comp_td[3]).html(), orderId: $(comp_td[0]).attr('class')},
function(data) {
	alert(data);
	$(comp_tr).remove();
	$('#trade_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
	$('#trade_stock > #stock_table > tbody > tr:even').css({'background-color':''});
});
});
$('#trade_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});

}

$('#tradedata > #but_buy').click(function(){
	$.post("server/trade.php", { trade: "buy", shareId: $('#trade_company > select option:selected').attr('value'), number: $('#tradedata > #in_setnumber').attr('value'), rate: $('#tradedata > #in_setquote').attr('value')},
	function(data) {
		alert(data);
		$.ajax({
			url: 'server/stockdata.php',
			success: function(stockdata) {
				$.post("server/trade.php",{},
				function(queuedata) {
				refresh_tradestock(stockdata,queuedata);
				});
			}
			});
		}
	);
});

$('#tradedata > #but_sell').click(function(){
	$.post("server/trade.php", { trade: "sell", shareId: $('#trade_company > select option:selected').attr('value'), number: $('#tradedata > #in_setnumber').attr('value'), rate: $('#tradedata > #in_setquote').attr('value')},
	function(data) {
		alert(data);
		$.ajax({
			url: 'server/stockdata.php',
			success: function(stockdata) {
				$.post("server/trade.php",{},
				function(queuedata) {
				refresh_tradestock(stockdata,queuedata);
				});
			}
			});
		});
});
var ref_x;
function setmarket_stockbox(){
$.ajax({
  url: 'server/stockdata.php',
  success: function(stockdata1) {
  stockdata = jQuery.parseJSON(stockdata1);
  company_tbody = "<tr title=\"sort\"><td>Company Name</td><td>Share Rate</td></tr>";
  for(x in stockdata){
  company_tbody+="<tr><td>"+stockdata[x]["name"]+"</td><td>"+stockdata[x]["marketValue"]+"</td></tr>";
  }
  $("#list_table > tbody").html(company_tbody);
  $('#list_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
  $('#list_table > tbody > tr >td:first-child').click(function(){
  company_name = $(this).html();
  if(this.innerHTML!="Company Name"){
  $.ajax({
  url: 'server/stockdata.php',
  success: function(stockdata1) {
  $('#marketdata > #but_buy').attr('disabled','disabled');
  //alert(stockdata1);
  stockdata = jQuery.parseJSON(stockdata1);
  for(x in stockdata){
  if(stockdata[x]["name"]==company_name){
	$('#market_bg > label').html("<b>"+stockdata[x]["name"]+"</b>");
	$('#company_details').html("Current Market Price : <b>"+stockdata[x]["marketValue"]+"</b> Exchange Price : <b>"+stockdata[x]["exchangePrice"]+"</b><br />Last Trade : <b>"+stockdata[x]["lastTrade"]+"</b> Day Low : <b>"+stockdata[x]["dayLow"]+"</b> Day High : <b>"+stockdata[x]["dayHigh"]+"</b><br />Total Shares Issues : <b>"+stockdata[x]["numIssued"]+"</b> Shares in Exchange : <b>"+stockdata[x]["sharesInExchange"]+"</b>");
	if(stockdata[x]["sharesInExchange"]>0){ref_x = x;
		$('#marketdata > #but_buy').removeAttr('disabled');
		//$('#marketdata > #but_buy').bind('click',function(evt){evt.preventDefault();});
		
		}
  }
  }
  }
  });
  }
  });
  }
});
}

$('#marketdata > #but_buy').click(function(){
			$.post("server/trade.php", { buyFromExchange: "true", shareId: stockdata[ref_x]["stockId"], number: $('#marketdata > #in_buyshares').attr('value')},
			function(data) {
				alert(data);
			});
		});

function set_rankingtable(ranklist1){
ranklist = jQuery.parseJSON(ranklist1);
rank_table = "<tr><td>Rank</td><td>Names</td><td>Net Worth</td></tr>";
for(x in ranklist){
rank_table+="<tr><td>"+ranklist[x]['rank']+"</td><td>"+ranklist[x]['Display_Name']+"</td><td>"+ranklist[x]['totalWorth']+"</td></tr>";
}
$('#ranktable_outer > #rank_table > tbody').html(rank_table);
$('#ranktable_outer > #rank_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
}

function setranking(){
$.ajax({
	url:'server/userdata.php?getDetail=ranklist',
	success:function(ranklist){
		set_rankingtable(ranklist);
		}
	});
}


function setbank(){
$.ajax({
	url: 'server/stockdata.php',
	success: function(stockdata) {
	$.post("server/bankdata.php", {},
			function(banklist) {
				setbank_stock(stockdata,banklist);
			});
	}
});
}

function settrade(){
$.ajax({
  url: 'server/stockdata.php',
  success: function(stockdata) {
	$.post("server/trade.php",{},
		function(queuedata) {
		settrade_stock(stockdata,queuedata);
		}
		);
    
  }
 });
}

$('#bankdata > #in_setnumber').blur(function(){
$.ajax({
	url:'server/stockdata.php',
	success:function(stockdata){
		stockdata = jQuery.parseJSON(stockdata);
		max=parseFloat(0.75 * stockdata[$('#mortgage_company > select > option:selected').attr('value')]['marketValue']) * parseFloat($('#bankdata > #in_setnumber').attr('value'));
		max = max.toFixed(2);
		$('#bankdata > #mortgage_company > #mortgage_max').html('Maximum Value : '+max);
		
	}
});
});

function refresh_bankstock(banklist1){
$('#bankdata > #in_setnumber').attr('value','');
$('#bankdata > #in_quote_value').attr('value','');
banklist = jQuery.parseJSON(banklist1);
stock_tbody="<tr title=\"sort\"><td>Company Name</td><td>Shares</td><td>Net value</td><td>(take back?)</td></tr>";
for(var x in banklist){
stock_tbody+="<tr><td class=\""+banklist[x]["mortgageId"]+"\">"+stockdata[banklist[x]["stockId"]]["name"]+"</td><td>"+banklist[x]["number"]+"</td><td>"+banklist[x]["loanValue"]+"</td><td><div class=\"bank_closer\"></div></td></tr>";
}
$("#bank_stock > #stock_table > tbody").html(stock_tbody);
$('#bank_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
$('.bank_closer').click(function(){
comp_tr=$(this).parent().parent();
comp_td=$(comp_tr).find('td');
$.post("server/bankdata.php", { recover: "", mortgageId: $(comp_td[0]).attr('class')},
function(data) {
	alert(data);
	$(comp_tr).remove();
	$('#bank_stock > #stock_table > tbody > tr:odd').css({'background-color':'#b5d2eb'});
	$('#bank_stock > #stock_table > tbody > tr:even').css({'background-color':''});
});
});

}

$('#but_mortgage').click(function(){
	$.post("server/bankdata.php", { mortgage: "", stockId: $('#mortgage_company > select option:selected').attr('value'), number: $('#bankdata > #in_setnumber').attr('value'), value: $('#bankdata > #in_quote_value').attr('value')},
	function(data) {
		alert(data);
		$.post("server/bankdata.php", {},
			function(banklist) {
				refresh_bankstock(banklist);
				});
			});
});

$('#icon_home').click(function(){
$.ajax({
  url: 'server/userdata.php',
  success: function(userdata) {
    sethome_user(userdata);
	getjson();
  }
});
$('.datas').css({'display':'none'});
$('#homedata').css({'display':'block'});
});
$('#icon_market').click(function(){
setmarket_stockbox();
$('.datas').css({'display':'none'});
$('#marketdata').css({'display':'block'});
});
$('#icon_trade').click(function(){
settrade();
$('.datas').css({'display':'none'});
$('#tradedata').css({'display':'block'});
});
$('#icon_ranking').click(function(){
setranking();
$('.datas').css({'display':'none'});
$('#rankdata').css({'display':'block'});
});
$('#icon_bank').click(function(){
setbank();
$('.datas').css({'display':'none'});
$('#bankdata').css({'display':'block'});
});
});
