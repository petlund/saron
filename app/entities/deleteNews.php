<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/News.php';

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = true;
    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
        exit();
    }


//    if($id<1){
//        $error = array();
//        $error["Result"] = "ERROR";
//        $error["Message"] = "Ingen rad är vald.";
//        echo json_encode($error);
//        exit();
//    } 

    try{
        $db = new db();
        $news = new News($db, $saronUser);
        $result = $news->delete(); 
        $db->dispose();            
        echo $result;
    }
    catch(Exception $error){
        echo $error->getMessage();        
        $db->dispose();            
    }    