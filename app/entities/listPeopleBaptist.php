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
        $PersonId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);
    
        try{
            $db = new db();
            $result =  $db->select($user, SQL_STAR_PEOPLE . ", " . setUserRoleInQuery($user), "FROM People ", "WHERE People.Id = " . $PersonId, "", "" );    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();
            $db = null;
        }        
    } 
    