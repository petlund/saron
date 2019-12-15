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
        $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{


        $HomeId = (int)filter_input(INPUT_GET, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        if($HomeId===0){
            $HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);        
        }
        if($HomeId===0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "HomeId = 0, File:  " . __FILE__;
            return json_encode($error);        
        }    

        $letter = (int)filter_input(INPUT_POST, "Letter", FILTER_SANITIZE_NUMBER_INT);

        $sqlUpdate = "UPDATE Homes ";
        $sqlSet = "SET ";
        $sqlSet.= "Letter='" . $letter . "' ";

        
        try{
            $db = new db();
            $db->transaction_begin();
            $updateResponse1 = $db->update($sqlUpdate, $sqlSet, "WHERE Id = " . $HomeId);
            $updateResponse2 = $db->update("UPDATE People ", "Set Updated=Now(), Updater=" . $saronUserId . " ", "Where HomeId=" . $HomeId, ""); 
            $selectResponse = $db->select($saronUser, "select Letter ,Id as HomeId ", "FROM Homes ", "WHERE Id = " . $HomeId, "", "");
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