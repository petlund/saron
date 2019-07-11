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
        if($PersonId===0){
            $PersonId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);        
        }
        if($PersonId===0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "PersonId = 0, File:  " . __FILE__;
            return json_encode($error);        
        }

        $dateOfBaptism = (String)filter_input(INPUT_POST, "DateOfBaptism", FILTER_SANITIZE_STRING);
        $baptister = (String)filter_input(INPUT_POST, "Baptister", FILTER_SANITIZE_STRING);
        $congregationOfBaptism = (String)filter_input(INPUT_POST, "CongregationOfBaptism", FILTER_SANITIZE_STRING);
        $congregationOfBaptismThis = (int)filter_input(INPUT_POST, "CongregationOfBaptismThis", FILTER_SANITIZE_NUMBER_INT);
        $comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);

        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";

        if(strlen($dateOfBaptism)>0){
            $sqlSet.= "DateOfBaptism='" . $dateOfBaptism . "', ";                
        }
        else{
            $sqlSet.= "DateOfBaptism=null, ";        
        }

        if(strlen($baptister)>0){
            $sqlSet.= "BaptisterEncrypt=AES_ENCRYPT('" . salt() . $baptister . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "BaptisterEncrypt=null, ";            
        }
        
        if($congregationOfBaptismThis===2){
            $sqlSet.= "CongregationOfBaptismThis=2, ";
            $sqlSet.= "CongregationOfBaptism='". FullNameOfCongregation . "', ";
        }
        else if($congregationOfBaptismThis === 1){
            $sqlSet.= "CongregationOfBaptism='" . $congregationOfBaptism . "', ";
            if(strlen($congregationOfBaptism) === 0){
                $error = array();
                $error["Result"] = "ERROR";
                $error["Message"] = "Du glömde att ange en dopförsamling.";
                echo json_encode($error);
                exit();
            }
            if($congregationOfBaptism === FullNameOfCongregation){
                $sqlSet.= "CongregationOfBaptismThis=2, ";
            }
            else{
                $sqlSet.= "CongregationOfBaptismThis=1, ";            
            }

        }
        else{
            $sqlSet.= "CongregationOfBaptismThis=0, ";
            $sqlSet.= "CongregationOfBaptism=null, ";            
        }

        if(strlen($dateOfBaptism) === 0 and strlen($comment) === 0 and $congregationOfBaptismThis > 0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Ge en kommentar till varför dopdatum saknas.";
            echo json_encode($error);
            exit();
        }    

        if(strlen($dateOfBaptism)  > 0 and $congregationOfBaptismThis === 0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Personen anges inte vara döpt, men har ett dopdatum .";
            echo json_encode($error);
            exit();
        }    

        if(strlen($comment)>0){
            $sqlSet.= "CommentEncrypt=AES_ENCRYPT('" . salt() . $comment . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "CommentEncrypt=null, ";
        }
        
        $sqlSet.= "Updater = ". $id . " ";
        try{
        $db = new db();
            $db->transaction_begin();
            $updateResponse1 = $db->update($sqlUpdate, $sqlSet, "WHERE Id = " . $PersonId);
            $selectResponse = $db->select($user, SQL_STAR_PEOPLE . ", " . DATES_AS_ALISAS_MEMBERSTATES, "FROM People ", "WHERE Id = " . $PersonId, "", "");
            $db->transaction_end();
            echo $selectResponse;
        }
        catch(Exception $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db = null;            
        }
    }