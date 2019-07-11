<?php
require_once SARON_ROOT . "app/database/db.php";
require_once SARON_ROOT . "app/access/wp-authenticate.php";

function ping(){
    $user = wp_get_current_user();    

    $ping =  "Databas: " . DATABASE;
    try{
        $db = new db();
        $result = $db->select($user,"select count(*) ", "from Statistics", "", "", "");
        $db = null;
        $ping.= " - Anslutning  OK!";
        echo $ping;
    }
    catch(Exception $error){
        $ping.=  " - Fel: " . $result;
        $db = null;
        return $ping;
    }
}
