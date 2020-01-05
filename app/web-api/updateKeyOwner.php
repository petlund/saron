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
        exit();
    }
    $Id = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
    $KeyToChurch = (int)filter_input(INPUT_POST, "KeyToChurch", FILTER_SANITIZE_NUMBER_INT);
    $KeyToExp = (int)filter_input(INPUT_POST, "KeyToExp", FILTER_SANITIZE_NUMBER_INT);
    $CommentKey = (String)filter_input(INPUT_POST, "CommentKey", FILTER_SANITIZE_STRING);


    try{
        $db = new db();
        $person = new Person($db, $saronUser);
        $result = $person->checkKeyHoldingData();
        if($result !== true){
            echo $result;
            exit();
        }                
        
        $db->transaction_begin();
        $respons = $person->updateKeyHoldning();
        $db->transaction_end();
        $db->dispose;                        
        echo $respons;
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
        $db->dispose();            
    } 