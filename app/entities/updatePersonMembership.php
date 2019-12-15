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
        $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        try{
            $db = new db();
            $person = new Person($db, $saronUser);
            $checkResult = $person->checkMembershipData();
            if($checkResult!==true){
                echo $checkResult;
                exit();
            }

            $db->transaction_begin();
            $response = $person->updateMembershipData();
//            $updateResponse1 = $db->update($sqlUpdate, $person->getUpdateMembershipSql($Id), "WHERE Id = " . $person->getCurrentPerson());
//            $selectResponse = $db->select($saronUser, SQL_STAR_PEOPLE . ", " . DATES_AS_ALISAS_MEMBERSTATES, "FROM People ", "WHERE Id = " . $PersonId, "", "");
            $db->transaction_end();
            $db->dispose();           
            echo $response;
        }
        catch(Exception $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db->dispose();           
        }
    }
 