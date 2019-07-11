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
    $user = wp_get_current_user();    

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $select = "SELECT 0 as Value, 'Nej' as DisplayText ";
        $select.= "Union "; 
        $select.= "SELECT 1 as Value, 'Ja, ange dopförsamling nedan.' as DisplayText ";
        $select.= "Union "; 
        $select.= "SELECT 2 as Value, 'Ja, i  " . FullNameOfCongregation . ".' as DisplayText ";

        try{
            $db = new db();
            $result = $db->select($user, $select, "", "", " ORDER BY Value ", "", "Options");    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();
            $db = null;
        }
    }