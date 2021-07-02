<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/OrganizationPos.php';

    
    try{
        $db = new db();
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_EDITOR_ROLE, REQUIRE_ORG_VIEWER_ROLE);
        $org = new OrganizationPos($db, $saronUser);
        $db->transaction_begin();
        $result = $org->addPerson();
        $db->transaction_end();            
        echo $result;
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        $result = $error->getMessage();
        echo $result;        
    }
