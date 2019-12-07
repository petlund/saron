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

        $HomeId = (int)filter_input(INPUT_GET, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        if($HomeId===0){
            $HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);        
        }
        if($HomeId===0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Hem inte definierat.";
            return json_encode($error);        
        }    

        $familyName = (String)filter_input(INPUT_POST, "FamilyName", FILTER_SANITIZE_STRING);
        $address = (String)filter_input(INPUT_POST, "Address", FILTER_SANITIZE_STRING);
        $phone = (String)filter_input(INPUT_POST, "Phone", FILTER_SANITIZE_STRING);
        $co = (String)filter_input(INPUT_POST, "Co", FILTER_SANITIZE_STRING);
        $city = (String)filter_input(INPUT_POST, "City", FILTER_SANITIZE_STRING);
        $zip = (String)filter_input(INPUT_POST, "Zip", FILTER_SANITIZE_STRING);
        $country = (String)filter_input(INPUT_POST, "Country", FILTER_SANITIZE_STRING);
        $letter = (int)filter_input(INPUT_POST, "Letter", FILTER_SANITIZE_NUMBER_INT);

        if(strlen($familyName)==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Det måste finnas ett Familjenamn.";
            echo json_encode($error);
            exit();
        }



        try{
            $db = new db();
            $db->transaction_begin();
            $updateResponse1 = $db->update($sqlUpdate, $sqlSet, "WHERE Id = " . $HomeId);
            $updateResponse2 = $db->update("UPDATE People ", "Set Updated=Now(), Updater=" . $id . " ", "Where HomeId=" . $HomeId, ""); 
            $selectResponse = $db->select($user, SQL_STAR_HOMES . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . NAMES_ALIAS_RESIDENTS, "FROM Homes ", "WHERE Id = " . $HomeId, "", "");
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