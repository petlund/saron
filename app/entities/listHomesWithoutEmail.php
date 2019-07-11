<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';

    header_remove(); 

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
    $user = wp_get_current_user();    

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
     
        $jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        $sqlLimit = "LIMIT " . $jtStartIndex . "," . $jtPageSize . ";";
        $jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        $sqlSorting = "Order by " . $jtSorting . " ";

        //, ' ', if(Mobile is not null, Concat('(', Mobile, ')'))
        $select ="Select Id as HomeId, ". ADDRESS_ALIAS_LONG_HOMENAME_MULTILINE . ", " . setUserRoleInQuery($user) . ", ";
        $select.="(SELECT GROUP_CONCAT(" . DECRYPTED_FIRSTNAME . ", ' ', " . DECRYPTED_LASTNAME . ", ' ', if(MobileEncrypt is not null, Concat('(', " . DECRYPTED_MOBILE . ", ')'), '') SEPARATOR '<BR>') FROM People where Homes.Id = HomeId order by DateOfBirth) as  Residents, ";
        $select.="Letter, ";
        $select.= DECRYPTED_ALIAS_PHONE . " ";
        $from   ="from Homes ";
        $where = "where ";
        $where.= "(Select count(*) from People where Homes.Id=People.HomeId and " . DECRYPTED_EMAIL . " like '%@%')=0 ";
        $where.= "and ";
        $where.= "(Select count(*) from People where Homes.Id=People.HomeId and DateOfMembershipStart is not null and DateOfMembershipEnd is null and DateOfDeath is null)>0 ";

        try{
            $db = new db();
            $result = $db->select($user, $select, $from, $where, $sqlSorting, $sqlLimit);    
            $db = null;
            echo $result;
        }
        catch(Exception $error){
            $db = null;
            echo $error->getMessage();
        }        
    }