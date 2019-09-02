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
    $user = wp_get_current_user();    

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $id=-1;
        if(isset( $user->ID )){
            $id = (int) $user->ID;
        }

        $person = new Person();
        $checkResult=$person->checkData();
        if($checkResult===true){
        }
        else{
            echo $checkResult;
        }
exit();
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
            $sql = SQL_STAR_PEOPLE . ", ". DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . DATES_AS_ALISAS_MEMBERSTATES;     
            
            $NewPersonId = $db->insert($sqlInsert, "People", "Id");

            $result = $db->select($user, $sql, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, "Where People.Id = " . $NewPersonId, "", "", "Record");

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