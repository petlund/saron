<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/Person.php';


    try{
        $db = new db();            
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_EDITOR_ROLE, REQUIRE_ORG_VIEWER_ROLE);

        $person = new Person($db, $saronUser);

        $personCheckResult = $person->checkPersonData();

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
        
        echo $respons;     
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
                    
    }
