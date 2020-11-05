<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/OrganizationVersion.php';

/*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
    $requireOrg = true;
    
    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole, $requireOrg)){
        echo notPermittedMessage();
        exit();
    }

    try{
        $db = new db();
        $db->transaction_begin();
        $orgVersion = new OrganizationVersion($db, $saronUser);
        $result = $orgVersion->insert();
        $db->transaction_end();            
        echo $result;
        $db->dispose();
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
        $db->dispose();
    }