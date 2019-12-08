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
    }
    else{
        $userId=-1;
        if(isset( $user->ID )){
            $userId = (int) $user->ID;
        }


        $sqlUpdate = "UPDATE People ";

        try{
            $db = new db();
    
            $person = new Person();
            $checkResult=$person->checkBaptistData();
            if($checkResult!==true){
                echo $checkResult;
                exit();
            }
            
            $db->transaction_begin();
            $updateResponse1 = $db->update($sqlUpdate, $person->getUpdateBaptimsSql($userId), "WHERE Id = " . $person->getCurrentPersonId());
            $selectResponse = $db->select($user, SQL_STAR_PEOPLE . ", " . DATES_AS_ALISAS_MEMBERSTATES, "FROM People ", "WHERE Id = " . $person->getCurrentPersonId(), "", "");
            $db->transaction_end();
            echo $selectResponse;
        }
        catch(Exception $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db->dispose();            
        }
    }