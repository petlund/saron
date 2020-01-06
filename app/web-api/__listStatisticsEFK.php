<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/Statistics.php';



    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
        exit();
    }

    try{
        $db = new db();
        $statistics = new Statistics($db, $saronUser);
        $results = $statistics->selectEFK();
        echo $results;
        $db->dispose();
    }
    catch(Exception $error){
        echo $error->getMessage();
        $db->dispose;
    }

