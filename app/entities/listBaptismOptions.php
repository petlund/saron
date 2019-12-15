<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
        $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $sql = "SELECT 0 as Value, 'Nej' as DisplayText ";
        $sql.= "Union "; 
        $sql.= "SELECT 1 as Value, 'Ja, ange dopförsamling nedan.' as DisplayText ";
        $sql.= "Union "; 
        $sql.= "SELECT 2 as Value, 'Ja, i  " . FullNameOfCongregation . ".' as DisplayText ";

        try{
            $db = new db();
            $result = $db->select($saronUser, $sql, "", "", " ORDER BY Value ", "", "Options");    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();
            $db = null;
        }
    }