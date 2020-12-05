<?php
require_once 'config.php'; 
require_once SARON_ROOT . "app/database/db.php";
require_once SARON_ROOT . "app/access/wp-authenticate.php";

function ping(){
    $db;
    
    $ping =  "Databas: " . DATABASE;
    try{
        $db = new db();
        $saronUser = new SaronUser($db);    
        $ping.= " - Anslutning  OK!";
        echo $ping;
    }
    catch(Exception $error){
        throw new Exception($error);
    }
}
