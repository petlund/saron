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

        $PersonId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        $Today = date("Y-m-d") ;

        if(strlen($PersonId)===0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Ingen person är vald.";
            echo json_encode($error);
            exit();
        }

        try{
            $db = new db();
            $db->transaction_begin();
            $result = $db->select($saronUser, "Select Id ", "From People ", "Where DateOfMembershipEnd is null and Id = " . $PersonId, "", "");
            $phpResult = json_decode($result);
            
            $sql = "update People set ";
            $sql.= "FirstNameEncrypt = AES_ENCRYPT('" . salt() . $Today . "', " . PKEY . "), ";
            $sql.= "LastNameEncrypt = AES_ENCRYPT('" . salt() . ANONYMOUS . "', " . PKEY . "), ";
            $sql.= "VisibleInCalendar = 0, ";
            $sql.= "EmailEncrypt = NULL, ";
            if($phpResult->TotalRecordCount ==='1'){
                $sql.= "DateOfMembershipEnd = '" . $Today . "', ";
            }
            $sql.= "MobileEncrypt = NULL, ";
            $sql.= "BaptisterEncrypt = NULL, ";
            $sql.= "CongregationOfBaptism = NULL, ";
            $sql.= "PreviousCongregation = NULL, ";
            $sql.= "NextCongregation = NULL, ";
            $sql.= "CommentEncrypt = NULL, ";
            $sql.= "Updater = ". $saronUser->ID . ", ";

            $sql.= "HomeId = NULL ";
            $sql.= "where Id=" . $PersonId;
            $deleteResult = $db->delete($sql); 
            $db->transaction_end();
            $db = null;
            echo $deleteResult;
        }
        catch(Exeption $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db = null;            
        }
    }    