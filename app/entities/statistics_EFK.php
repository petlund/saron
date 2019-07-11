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
        $sql = getIntervallSql(11, 20);
        $sql.= " union (";
        $sql.= getIntervallSql(21, 30);
        $sql.= ") union (";
        $sql.= getIntervallSql(31, 40);
        $sql.= ") union (";
        $sql.= getIntervallSql(41, 50);
        $sql.= ") union (";
        $sql.= getIntervallSql(51, 60);
        $sql.= ") union (";
        $sql.= getIntervallSql(61, null);
        $sql.= ") union (";
        $sql.= getIntervallSql(null, null);
        $sql.= ") ";

        $jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        if(Strlen($jtSorting)>0){
            $sqlOrderBy = "ORDER BY " . $jtSorting . " ";
        }
        else{
            $sqlOrderBy = "";
        }

        $sql.= $sqlOrderBy;

        $sqlCount = "select 6 as c"; 

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

    function getIntervallSql($minAge, $maxAge){
        $sqlInterval="";
        $ageAlias = " as AgeInterval, ";
        $sqlWhereInterval = "and extract(year from now())-extract(year from DateOfBirth) "; 
        if($minAge === null and $maxAge===null){
            $sqlLabel="Select 'Totalt' " . $ageAlias;
            $sqlInterval = $sqlWhereInterval . ">=11";
        }
        else{
            if($maxAge!==null){
                $sqlLabel="Select '" . $minAge . "-" . $maxAge ."'" . $ageAlias;        
                $sqlInterval = $sqlWhereInterval . "between ".  $minAge . " and " . $maxAge;
            }
            else{
                $sqlLabel="Select '" . $minAge . "-...'" . $ageAlias;        
                $sqlInterval = $sqlWhereInterval . ">= " . $minAge;
            }
        }
        $sqlCount = "count(*) as Amount from People where ";
        $sqlCount.= "extract(year from now()) > extract(year from DateOfMembershipStart) and ";
        $sqlCount.= "(extract(year from now()) = extract(year from DateOfMembershipEnd) or DateOfMembershipEnd is null)"; 

        $sql = $sqlLabel . $sqlCount . $sqlInterval;

        return $sql;
    }
    