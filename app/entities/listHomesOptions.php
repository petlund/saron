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
        $HomeId = (int)filter_input(INPUT_GET, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        $where ="";
        if($HomeId===0){
            $sql = "SELECT 0 as Value, ' Inget hem' as DisplayText "; 
            $sql.= "Union "; 
            $sql.= "SELECT -1 as Value, '  Nytt hem (Lägg till adress och hemtelefon)' as DisplayText ";
            $sql.= "Union "; 
            $sql.= "select Id as Value, " . ADDRESS_ALIAS_LONG_HOMENAME;
        }
        else{
            $sql.= "select Id as Value, " . ADDRESS_ALIAS_LONG_HOMENAME;
            $where = "WHERE Value=" . $HomeId;
        }
        try{
            $db = new db();
            $result = $db->select($user, $sql, "FROM Homes ", $where, "ORDER BY DisplayText ", "", "Options");    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();
            $db = null;
        }
    }