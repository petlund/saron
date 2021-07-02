<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/OrganizationUnit.php';


    try{
        $db = new db(); 
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE);
        $org = new OrganizationUnit($db, $saronUser);
        $result = $org->select();    
        
        echo $result;        
    }
    catch(Exception $error){
        echo $error->getMessage();        
        
    }