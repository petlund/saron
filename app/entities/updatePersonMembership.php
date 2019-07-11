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
            echo json_encode($error);   
            exit();
        }
        $previousCongregation = (String)filter_input(INPUT_POST, "PreviousCongregation", FILTER_SANITIZE_STRING);
        $membershipNo = (int)filter_input(INPUT_POST, "MembershipNo", FILTER_SANITIZE_NUMBER_INT);
        $dateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
        $dateOfMembershipEnd = (String)filter_input(INPUT_POST, "DateOfMembershipEnd", FILTER_SANITIZE_STRING);
        $nextCongregation = (String)filter_input(INPUT_POST, "NextCongregation", FILTER_SANITIZE_STRING);
        $comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);

        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "PreviousCongregation='" . $previousCongregation . "', ";

        if(strlen($dateOfMembershipStart)===0 and strlen($dateOfMembershipEnd)!==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Personen måste ha ett datum för medlemskapets start om den ska ha ett slutdatum för medlemskapet.";
            echo json_encode($error);
            exit();
        }

        if($membershipNo === 0 and strlen($dateOfMembershipStart)!==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Personen har ett datum för start av medlemskap men saknar medlemsnummer. Lägg till ett medlemsnummer.";
            echo json_encode($error);
            exit();
        }

        if($membershipNo > 0 and strlen($dateOfMembershipStart)===0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Personen har ett inget datum för start av medlemskap men har ett medlemsnummer. Lägg till ett datum för start av medlemskap.";
            echo json_encode($error);
            exit();
        }

        if($VisibleInCalendar===0 and strlen($dateOfMembershipStart)!==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Ange om personen ska vara synlig i adresskalendern eller ej.";
            echo json_encode($error);
            exit();
        }

        if($VisibleInCalendar===2 and strlen($dateOfMembershipEnd)!==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Endast medlemmar ska vara synliga i adresskalendern.";
            echo json_encode($error);
            exit();
        }

        if($membershipNo===0){
            $sqlSet.= "MembershipNo=null, ";
        }
        else{
            $sqlSet.= "MembershipNo=" . $membershipNo . ", ";
        }    

        if(strlen($dateOfMembershipStart)>0){
            $sqlSet.= "DateOfMembershipStart='" . $dateOfMembershipStart . "', ";
        }
        else{
            $sqlSet.= "DateOfMembershipStart=null, ";
        }
        if(strlen($dateOfMembershipEnd)>0){
            $VisibleInCalendar=1;
            $sqlSet.= "DateOfMembershipEnd='" . $dateOfMembershipEnd . "', ";
            $sqlSet.= "VisibleInCalendar=1, ";
        }
        else{
            $sqlSet.= "DateOfMembershipEnd=null, ";        
            $sqlSet.= "VisibleInCalendar=" . $VisibleInCalendar . ", ";
    }
        $sqlSet.= "NextCongregation='" . $nextCongregation . "', ";

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
 