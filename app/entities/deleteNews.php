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
        $userId = (int)filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
        $Today = date("Y-m-d") ;

        $sql = "delete from News where id=" . $userId;

        if($userId<1){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Ingen rad är vald.";
            echo json_encode($error);
            exit();
        } 

        try{
            $db = new db();
            $result = $db->delete($sql); 
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();        
            $db = null;            
        }
    }    