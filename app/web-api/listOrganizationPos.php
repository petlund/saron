<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config.php'; 
require_once SARON_ROOT . "app/access/cookie.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/OrganizationPos.php';

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = 0;
    $requireOrg = 0;
    
    try{
        $db = new db(); 
        $saronUser = new SaronUser($db, $requireEditorRole, $requireOrg);
        $org = new OrganizationPos($db, $saronUser);
        $result = $org->select();    
        $db->dispose();
        echo $result;        
    }
    catch(Exception $error){
        echo $error->getMessage();        
        $db->dispose();
    }