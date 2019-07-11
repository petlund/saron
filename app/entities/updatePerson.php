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
        exit();
    }
    
    $id=-1;
    if(isset( $user->ID )){
        $id = (int) $user->ID;
    }

    $PersonId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
    $HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
    $LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
    $FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
    $DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
    $DateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
    $DateOfMembershipEnd = (String)filter_input(INPUT_POST, "DateOfMembershipEnd", FILTER_SANITIZE_STRING);
    $Gender = (int)filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_NUMBER_INT);
    $VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);
    $Email = (String)filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
    $Mobile = (String)filter_input(INPUT_POST, "Mobile", FILTER_SANITIZE_STRING);
    $Phone = (String)filter_input(INPUT_POST, "Phone", FILTER_SANITIZE_STRING);
    $DateOfDeath = (String)filter_input(INPUT_POST, "DateOfDeath", FILTER_SANITIZE_STRING);
    $Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);

    if(strlen($FirstName)==0 or strlen($LastName)==0 or strlen($DateOfBirth)==0){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "Personen behöver ett för- och ett efternamn samt ett födelsedadum för att kunna lagras i registret";
        echo json_encode($error);
        exit();
    }

    if($VisibleInCalendar === 2){
        if((strlen($DateOfMembershipStart)===0 and strlen($DateOfMembershipEnd)===0) or strlen($DateOfMembershipEnd)!==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Icke medlemmar ska inte vara synliga i adresskalendern.";
            echo json_encode($error);
            exit();
        }
    }

    
    $sqlUpdate = "UPDATE People ";
    $sqlSet = "SET ";
    $newHomeId=0;    
    try{
        $db = new db();
        $db->transaction_begin();
        if($db->exist($FirstName, $LastName, $DateOfBirth, $PersonId));
        $oldHomeId=$HomeId;        // reference UI update 
        Switch ($HomeId){  
            case 0: //inget hem
                $newHomeId=0;            // person left his home
                break;
            case -1: //Nytt hem 
                $newHomeId = $db->insert("INSERT INTO Homes (FamilyNameEncrypt) VALUES (AES_ENCRYPT('" . salt() . $LastName . "', " . PKEY . "));", "Homes", "Id"); // person move to new home
                break; 
            Default:
                $newHomeId=$HomeId;        // Person stay in his home 
        }
        
        $sqlSet.= "LastNameEncrypt=AES_ENCRYPT('" . salt() . $LastName . "', " . PKEY . "), ";
        $sqlSet.= "FirstNameEncrypt=AES_ENCRYPT('" . salt() . $FirstName . "', " . PKEY . "), ";
        $sqlSet.= "DateOfBirth='" . $DateOfBirth . "', ";
        $sqlSet.= "Gender=" . $Gender . ", ";
        if(strlen($DateOfDeath)>0){    
            $newHomeId=0;
            $Email = null;
            $Mobile = null;
            if(strlen($DateOfMembershipEnd)===0){
                $DateOfMembershipEnd = $DateOfDeath;
            }
        }
        if(strlen($DateOfMembershipEnd) > 0){    
            $VisibleInCalendar=1;            
        }

        if(strlen($Mobile)>0){
            $sqlSet.= "MobileEncrypt=AES_ENCRYPT('" . salt() . $Mobile . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "MobileEncrypt=null, ";            
        }
        
        if(strlen($Email)>0){
            $sqlSet.= "EmailEncrypt=AES_ENCRYPT('" . salt() . $Email . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "EmailEncrypt=null, ";            
        }
        
        if(strlen($DateOfMembershipStart) > 0){                
            $sqlSet.= "DateOfMembershipStart='" . $DateOfMembershipStart . "', ";        
        }
        else{
            $sqlSet.= "DateOfMembershipStart=null, ";        
        }
            
        if(strlen($DateOfMembershipEnd) > 0){                
            $sqlSet.= "DateOfMembershipEnd='" . $DateOfMembershipEnd . "', ";        
        }
        else{
            $sqlSet.= "DateOfMembershipEnd=null, ";        
        }
            
        if(strlen($DateOfDeath) > 0){                
            if(strlen($DateOfMembershipEnd) === 0){                
                $sqlSet.= "DateOfMembershipEnd='" . $DateOfDeath . "', ";        
            }
            $sqlSet.= "DateOfDeath='" . $DateOfDeath. "', ";        
        }
        else{
            $sqlSet.= "DateOfDeath=null, ";        
        }
            
        $sqlSet.= "HomeId=" . $newHomeId . ", ";
        $sqlSet.= "VisibleInCalendar=" . $VisibleInCalendar . ", ";
        
        if(strlen($Comment)>0){
            $sqlSet.= "CommentEncrypt=AES_ENCRYPT('" . salt() . $Comment . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "CommentEncrypt=null, ";
        }

        $sqlSet.= "Updater = ". $id . " ";

        $selectResponse = $db->update($sqlUpdate, $sqlSet, "WHERE Id = " . $PersonId);
        $selectResponse = $db->select($user, SQL_STAR_PEOPLE . ", " . $oldHomeId . " as oldHomeId, " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", "  . DECRYPTED_ALIAS_PHONE . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", " . NAMES_ALIAS_RESIDENTS, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, "WHERE People.Id = " . $PersonId, "", "");
        $db->transaction_end();
        $db=null;
        echo $selectResponse;
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
        $db = null;            
    } 