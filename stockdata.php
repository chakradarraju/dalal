<?php

require_once("users.php");

if(getLoggedInUserId()==-1) { //If not logged in
    die("Your session expired<br />Please login again");
}

?>
