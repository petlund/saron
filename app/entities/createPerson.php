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
    $user = wp_get_current_user();    

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
        exit();
    }
    try{
        $db = new db();            

        $person = new Person($db, $user);

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

        //$NewPersonId = $db->insert($sqlInsert, "People", "Id");

        //$sql = SQL_STAR_PEOPLE . ", ". DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . DATES_AS_ALISAS_MEMBERSTATES;     
        //$result = $db->select($user, $person->getSelectPersonSql(), SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, "Where People.Id = " . $NewPersonId, "", "", "Record");

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
