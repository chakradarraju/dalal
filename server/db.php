<?php

require_once("config.php");

$connect = mysql_connect(MYSQL_SERVER,MYSQL_USERNAME,MYSQL_PASSWORD);
if(!$connect) die("Could not connect to mysql server");
mysql_select_db(MYSQL_DATABASE) or die("Could not open database");

?>
