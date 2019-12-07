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

    
    $newHomeId=0;    
    try{
        $db = new db();
        $db->transaction_begin();
        $db->exist($FirstName, $LastName, $DateOfBirth, $PersonId);
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