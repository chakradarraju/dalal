<?php

require_once("config.php");

$connect = mysql_connect(MYSQL_SERVER,MYSQL_USERNAME,MYSQL_PASSWORD);
if(!$connect) die(json_encode(array("error" => "Could not connect to mysql server")));
mysql_select_db(MYSQL_DATABASE) or die(json_encode(array("error" =>"Could not open database")));

?>
