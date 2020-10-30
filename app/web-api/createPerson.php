<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/Person.php';

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = true;
    $requireOrg = false;

    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole, $requireOrg)){
        echo notPermittedMessage();
        exit();
    }
    try{
        $db = new db();            

        $person = new Person($db, $saronUser);

        $personCheckResult = $person->checkPersonData();
        $db->php_dev_error_log(__FILE__, $personCheckResult);

        if($personCheckResult !== true){
            echo $personCheckResult;
            exit();
        }

        $membershipCheckResult = $person->checkMembershipData();
        if($membershipCheckResult !== true){
            echo $membershipCheckResult;
            exit();
        }

        $db->transaction_begin();

        $respons = $person->insert();

        $db->transaction_end();
        $db->dispose();
        echo $respons;     
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
        $db->dispose();            
    }
