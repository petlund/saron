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

        $HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        $LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
        $FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
        $DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
        $Gender = (int)filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_NUMBER_INT);
        $Email = (String)filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
        $Mobile = (String)filter_input(INPUT_POST, "Mobile", FILTER_SANITIZE_STRING);
        $DateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
        $MembershipNo = (int)filter_input(INPUT_POST, "MembershipNo", FILTER_SANITIZE_NUMBER_INT);
        $VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);    
        $Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);

        $sqlInsert = "INSERT INTO People (LastNameEncrypt, FirstNameEncrypt, DateOfBirth, Gender, EmailEncrypt, MobileEncrypt, DateOfMembershipStart, MembershipNo, VisibleInCalendar, CommentEncrypt, Inserter, HomeId) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "AES_ENCRYPT('" . salt() . $LastName . "', " . PKEY . "), ";
        $sqlInsert.= "AES_ENCRYPT('" . salt() . $FirstName . "', " . PKEY . "), ";
        $sqlInsert.= "'" . $DateOfBirth . "', ";
        $sqlInsert.= "" . $Gender . ", ";
        
        if(strlen($Email)>0){
            $sqlInsert.= "AES_ENCRYPT('" . salt() . $Email . "', " . PKEY . "), ";
        }
        else{
            $sqlInsert.= "null, ";                    
        }
        
        if(strlen($Mobile)>0){
            $sqlInsert.= "AES_ENCRYPT('" . salt() . $Mobile . "', " . PKEY . "), ";
        }
        else{
            $sqlInsert.= "null, ";                    
        }
        
        if(strlen($DateOfMembershipStart)>0){    
            $sqlInsert.= "'" . $DateOfMembershipStart . "', ";
        } 
        else{ 
            $sqlInsert.= "null, ";        
        }

        if($MembershipNo===0){
            $sqlInsert.= "null, ";
        }
        else{
            $sqlInsert.= $MembershipNo . ", ";
        }    

        $sqlInsert.= $VisibleInCalendar . ", ";
        
        if(strlen($Comment)>0){
            $sqlInsert.= "AES_ENCRYPT('" . salt() . $Comment . "', " . PKEY . "), ";
        }
        else{
            $sqlInsert.= "null, ";                    
        }
        
        $sqlInsert.= $id . ", ";


        if(strlen($FirstName)==0 or strlen($LastName)==0 or strlen($DateOfBirth)==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Personen behöver ett för- och ett efternamn samt ett födelsedadum för att kunna lagras i registret";
            echo json_encode($error);
            exit();
        }

        if($MembershipNo < 1 and strlen($DateOfMembershipStart)!==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Personen har ett datum för start av medlemskap men saknar medlemsnummer. Lägg till ett medlemsnummer.";
            echo json_encode($error);
            exit();
        }

        if($VisibleInCalendar === 0 and strlen($DateOfMembershipStart)!==0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Ange om personen ska vara synlig i adresskalendern eller ej.";
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

        if($MembershipNo > 0 and strlen($DateOfMembershipStart)===0){
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Personen har ett inget datum för start av medlemskap men har ett medlemsnummer. Lägg till ett datum för start av medlemskap.";
            echo json_encode($error);
            exit();
        }

        try{
            $db = new db();            
            $db->transaction_begin();
            $db->exist($FirstName, $LastName, $DateOfBirth);
            Switch ($HomeId){
                case 0: //inget hem
                    $sqlInsert.= "null"; 
                    break;
                case -1: //Nytt hem
                    $NewHomeId = $db->insert("INSERT INTO Homes (FamilyNameEncrypt) VALUES (AES_ENCRYPT('" . salt() . $LastName . "', " . PKEY . "))", "Homes", "Id"); // New person i new Home
                    $sqlInsert.= $NewHomeId;                                                
                    break; 
                Default: //befintligt hem
                    $sqlInsert.= $HomeId;        
            }

            $sqlInsert.= ")";
            $select = SQL_STAR_PEOPLE . ", ". DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . DATES_AS_ALISAS_MEMBERSTATES;     
            
            $NewPersonId = $db->insert($sqlInsert, "People", "Id");

            $result = $db->select($user, $select, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, "Where People.Id = " . $NewPersonId, "", "", "Record");

            $db->transaction_end();
            echo $result;     
            $db = null;
        }
        catch(Exception $error){
            $db->transaction_roll_back();
            $db->transaction_end();
            echo $error->getMessage();        
            $db = null;            
        }
    }