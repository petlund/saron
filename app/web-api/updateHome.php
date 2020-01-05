<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/Home.php';


    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = true;
        $saronUser = new SaronUser(wp_get_current_user());  

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
        exit();
    }

    try{
        $db = new db();
        $home = new Home($db, $saronUser);
        $home->checkHomeData();
        $db->transaction_begin();

        $response = $home->update();
        
        $db->transaction_end();
        $db->dispose();                        
        echo $response;
    }
    catch(Exception $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
        $db->dispose();                        
    }
     