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
        $curYear = (int)filter_input(INPUT_GET, "year", FILTER_SANITIZE_NUMBER_INT);
        
        $jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        if(Strlen($jtSorting)>0){
            $sqlOrderBy = " ORDER BY " . $jtSorting . " ";
        }
        else{
            $sqlOrderBy = "";
        }        
        $jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);

        if($jtPageSize>0){
            $sqlLimit = " LIMIT " . $jtStartIndex . ", " . $jtPageSize . ";";
        }
        else{    
            $sqlLimit = "";
        }
        $sqlSelect="SELECT " . DECRYPTED_ALIAS_LASTNAME . ", " . DECRYPTED_ALIAS_FIRSTNAME . ", " . DECRYPTED_ALIAS_COMMENT . ", DateOfBirth, ";
        $sql =$sqlSelect;  
        $sql.="DateOfMembershipStart as 'event_date', 
            'Ny' as event_type, 1 as 'Diff' 
            FROM `People` as p  
            WHERE extract(YEAR from DateOfMembershipStart)=" . $curYear;
        $sql.=" UNION ";
        $sql.=$sqlSelect;  
        $sql.="DateOfMembershipEnd as 'event_date', 
            'Avslutad' as event_type, -1 as 'Diff' 
            FROM `People` as p  
            WHERE DateOfDeath is null and DateOfMembershipStart is not null and extract(YEAR from DateOfMembershipEnd)=" . $curYear;   
        $sql.=" UNION ";
        $sql.=$sqlSelect;  
        $sql.="DateOfBaptism as 'event_date', 'Döpt' as event_type, 0 as 'Diff' 
            FROM `People` as p  
            WHERE CongregationOfBaptismThis=2 and extract(YEAR from DateOfBaptism)=" . $curYear; 
        $sql.=" UNION ";
        $sql.=$sqlSelect;  
        $sql.="DateOfDeath as 'event_date', 'Avliden' as event_type, -1 as 'Diff' 
            FROM `People` as p  
            WHERE extract(YEAR from DateOfDeath)=" . $curYear;  
        $sql.= $sqlOrderBy;
        $sql.= $sqlLimit;
        
        
        $sqlCount  = "select ";        
        $sqlCount .= "(SELECT count(*) FROM `People` as p1 WHERE extract(YEAR from p1.DateOfMembershipStart)=" . $curYear . ") + ";
        $sqlCount .= "(SELECT count(*) FROM `People` as p2 WHERE extract(YEAR from p2.DateOfMembershipEnd)=" . $curYear . ") + ";
        $sqlCount .= "(SELECT count(*) FROM `People` as p4 WHERE extract(YEAR from p4.DateOfDeath)=" . $curYear . ") + ";
        $sqlCount .= "(SELECT count(*) FROM `People` as p3 WHERE p3.CongregationOfBaptismThis=2 and extract(YEAR from p3.DateOfBaptism)=" . $curYear . ") "; 
        $sqlCount .= "as c;";
        
        try{
            $db = new db();
            $result = $db->selectSeparate($user, $sql, $sqlCount);    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            echo $error->getMessage();
            $db = null;
        }
    }

    
    