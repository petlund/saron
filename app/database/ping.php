<?php
require_once 'config.php'; 
require_once SARON_ROOT . "app/database/db.php";
require_once SARON_ROOT . "app/access/wp-authenticate.php";

function ping(){
    $db = null;
    $saronUser = new SaronUser(wp_get_current_user());    
    
    $ping =  "Databas: " . DATABASE;
    try{
        $db = new db();
        $result = $db->select($saronUser,"select count(*) ", "from Statistics", "", "", "");
        $db->dispose();
        $ping.= " - Anslutning  OK!";
        echo $ping;
    }
    catch(Exception $error){
        $ping.=  " - Fel: " . $error->getMessage();
        $db->dispose();
        return $ping;
    }
}
