<?php

function dalalErrorLog($errorMessage) {
    $errorLog = fopen("./logs/errorLog",'a') or die(json_encode(array("error" => "Error occured, please approach event managers if you notice something went wrong")));
    $errorMessage = "<<<<<<ERROR START\n".$errorMessage."\n>>>>>>ERROR END";
    fwrite($errorLog,$errorMessage);
    fclose($errorLog);
    return true;
}

function runQueries($query) {
    $singlequeries = explode(";\n",$query);
    $failed = false;
    foreach($singlequeries as $singlequery) {
        if(trim($singlequery)!="") {
            $result = mysql_query($singlequery);
            if(!$result) {
                mysql_query("ROLLBACK") or dalalErrorLog("$query\n".mysql_error());
                $failed = true;
                break;
            }
        }
    }
    return $failed;
}

?>
