<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/Statistics.php'; 


    try{
        $db = new db();
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE);
        $statistics = new Statistics($db, $saronUser);
        $db->transaction_begin();
        $result = $statistics->select();    
        $db->transaction_end();
        
        echo $result;

    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();
        
    }

    
    