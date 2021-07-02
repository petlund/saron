<?php
require_once 'config.php'; 
require_once SARON_ROOT . "app/database/db.php";
require_once SARON_ROOT . "app/entities/SaronUser.php";
require_once SARON_ROOT . "app/access/wp-authenticate.php";

function ping(){
    $db;
    
    $ping =  "Databas: " . DATABASE;
    try{
        $db = new db();
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE);            
        $ping.= " - Anslutning  OK!";
        echo $ping;
    }
    catch(Exception $error){
        $ping.= "FEL!";
//        throw new Exception($error);
    }
}
