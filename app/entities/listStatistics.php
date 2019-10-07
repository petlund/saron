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
            $sqlSelect = "SELECT year, number_of_members, number_of_new_members, number_of_finnished_members, number_of_dead, number_of_baptist_people, format(average_age, 1) as avg_age, format(average_membership_time, 1) as avg_membership_time, diff ";
            //$sqlSelect = "SELECT * ";
            $result = $db->select($user, $sqlSelect, "From Statistics ", "", $sqlOrderBy,  $sqlLimit);    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();
            $db = null;
        }
        
    }

    
    