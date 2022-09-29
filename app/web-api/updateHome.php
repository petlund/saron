<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/Home.php';


    try{
        $db = new db();
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_EDITOR_ROLE, REQUIRE_ORG_VIEWER_ROLE);
        $home = new Home($db, $saronUser);

        $db->transaction_begin();
        $response = $home->update();       
        $db->transaction_end();
        echo $response;
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
    }
     