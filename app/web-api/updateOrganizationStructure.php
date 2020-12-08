<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/SaronCookie.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/OrganizationStructure.php';

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = 0;
    $requireOrg = 1;    
    

    try{
        $db = new db();
        $db->transaction_begin();
        $saronUser = new SaronUser($db, $requireEditorRole, $requireOrg);
        $org = new OrganizationStructure($db, $saronUser);
        $respons = $org->update();        
        $db->transaction_end();
                    
        echo $respons;
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
                    
    } 