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
        $writerName = 'Saknas';

        if(isset( $user->ID )){
            $writerName = $user->user_firstname . " " . $user->user_lastname ;
        }

        $Id = (int)filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
        $information = (String)filter_input(INPUT_POST, "information", FILTER_SANITIZE_STRING);

        $sqlUpdate = "UPDATE News ";
        $sqlSet = "SET ";
        $sqlSet.= "information='" . $information . "', ";
        $sqlSet.= "writer='" . $writerName . "' ";

        try{
            $db = new db();
            $db->transaction_begin();
            $selectResponse = $db->update($sqlUpdate, $sqlSet, "WHERE id = " . $Id);
            $selectResponse = $db->select($user, "select * , " . setUserRoleInQuery($user) , "FROM News ", "WHERE id = " . $Id, "", "");
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