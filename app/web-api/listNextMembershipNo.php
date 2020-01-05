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
        $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $PersonId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);

        if(strlen($PersonId)==0){
            $PersonId="null";
        }

        $sql = "SELECT 0 as Value, '[Inget medlemsnummer]' as DisplayText, 1 as ind ";
        $sql.= "Union "; 
        $sql.= "select MembershipNo as Value, Concat(MembershipNo, ' [Nuvarande]') as DisplayText, 2 as ind From People Where MembershipNo>0 and Id = " . $PersonId . " ";
        $sql.= "Union "; 
        $sql.= "select if(max(MembershipNo) is null, 0, max(MembershipNo)) + 1 as Value, CONCAT(if(max(MembershipNo) is null, 0, max(MembershipNo)) + 1, ' [Första lediga]') as DisplayText, 3 as ind ";

        try{
            $db = new db();
            $result = $db->select($saronUser, $sql, "FROM People ", "", "ORDER BY ind ", "", "Options");
            $db->dispose();
            echo $result;
        }
        catch(Exception $error){
            $db->dispose();
            echo $error->getMessage();
        }
    }