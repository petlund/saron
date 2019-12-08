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

        $information = (String)filter_input(INPUT_POST, "information", FILTER_SANITIZE_STRING);

        $sqlInsert = "INSERT INTO News (information, writer) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $information . "', ";
        $sqlInsert.= "'" . $writerName . "')";

        try{
            $db = new db();
            $db->transaction_begin();            
            $userId = $db->insert($sqlInsert, "News", "id");
            $userRole = setUserRoleInQuery($user); 
            $result = $db->select($user, "select *, " . $userRole , "FROM News ", "WHERE id = " . $userId, "", "", "Record");
            $db->transaction_end();            
            echo $result;
            $db=null;
        }
        catch(Exception $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db = null;            
        }
    }
