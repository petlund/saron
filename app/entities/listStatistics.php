<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once 'updateStatistics.php'; 


    $updateStatistics = new updateStatistics();
    $updateStatistics->deleteEmptyHomes();
    $updateStatistics->addNewYearIfNeeded(); 
    $updateStatistics->updateCurrentYear();

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
    $user = wp_get_current_user();    

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{            
        $jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        if(Strlen($jtSorting)>0){
            $sqlOrderBy = "ORDER BY " . $jtSorting . " ";
        }
        else{
            $sqlOrderBy = "";
        }        
        $jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);

        if($jtPageSize>0){
            $sqlLimit = "LIMIT " . $jtStartIndex . "," . $jtPageSize . ";";
        }
        else{    
            $sqlLimit = "";
        }
        
        try{
            $db = new db();
            $result = $db->select($user, "Select * ", "From Statistics ", "", $sqlOrderBy,  $sqlLimit);    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();
            $db = null;
        }
        
    }

    
    