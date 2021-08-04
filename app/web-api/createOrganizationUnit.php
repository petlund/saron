<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/OrganizationUnit.php';

    
    try{
        $db = new db();
        $db->transaction_begin();
        $saronUser = new SaronUser($db);        
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_EDITOR_ROLE);
        $org = new OrganizationStructure($db, $saronUser);
        $result = $org->insert();
        $db->transaction_end();            
        echo $result;
        
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
        
    }
