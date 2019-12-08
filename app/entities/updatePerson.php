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
    
    $userId=-1;
    if(isset( $user->ID )){
        $userId = (int) $user->ID;
    }
    
    try{
        $db = new db();
        $person = new Person($db);

        $checkResult = $person->checkPersonData($db);
        if($checkResult!==true){
            echo $checkResult;
            exit();
        }

        $db->transaction_begin();
        $person->updatePersonData($userId);
        $selectResponse = $db->select($user, SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", "  . DECRYPTED_ALIAS_PHONE . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", " . NAMES_ALIAS_RESIDENTS, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, "WHERE People.Id = " . $person->getCurrentPersonId(), "", "");
        $db->transaction_end();
        $db->dispose();
        echo $selectResponse;
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
        $db = null;            
    } 