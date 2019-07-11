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
        $Id = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        $KeyToChurch = (int)filter_input(INPUT_POST, "KeyToChurch", FILTER_SANITIZE_NUMBER_INT);
        $KeyToExp = (int)filter_input(INPUT_POST, "KeyToExp", FILTER_SANITIZE_NUMBER_INT);
        $CommentKey = (String)filter_input(INPUT_POST, "CommentKey", FILTER_SANITIZE_STRING);

        if(($KeyToExp === 2 or $KeyToChurch === 2) and strlen($CommentKey)<5){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Du behöver ange en längre kommentar för nyckelinnehavet.";
            echo json_encode($error);
            exit();
        } 

        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "KeyToChurch='" . $KeyToChurch . "', ";
        $sqlSet.= "KeyToExp='" . $KeyToExp . "', ";
        
        if(strlen($CommentKey)>0){
            $sqlSet.= "CommentKeyEncrypt=AES_ENCRYPT('" . salt() . $CommentKey . "', " . PKEY . ") ";
        }
        else{
            $sqlSet.= "CommentKeyEncrypt=null ";            
        }
            
        $sqlWhere = "WHERE id = " . $Id;
        try{
            $db = new db();
            $db->transaction_begin();
            $selectResponse = $db->update($sqlUpdate, $sqlSet, $sqlWhere);
            $result =  $db->select($user, "Select Id, KeyToExp, KeyToChurch, " . DECRYPTED_ALIAS_COMMENT_KEY . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " .  setUserRoleInQuery($user) , " FROM People ", $sqlWhere, "", "", "Records");    
            $db->transaction_end();
            $db = null;                        
            echo $result;
        }
        catch(Exception $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db = null;            
        }
    } 