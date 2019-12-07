<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';


    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = true;
    $user = wp_get_current_user();    

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $id=-1;
        if(isset( $user->ID )){
            $id = (int) $user->ID;
        }

        $PersonId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
//        if($PersonId===0){
//            $PersonId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);        
//        }


        try{
            $db = new db();
            $db->transaction_begin();
            $updateResponse1 = $db->update($sqlUpdate, $sqlSet, "WHERE Id = " . $PersonId);
            $selectResponse = $db->select($user, SQL_STAR_PEOPLE . ", " . DATES_AS_ALISAS_MEMBERSTATES, "FROM People ", "WHERE Id = " . $PersonId, "", "");
            $db->transaction_end();
            $db = null;           
            echo $selectResponse;
        }
        catch(Exception $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db = null;            
        }
    }
 