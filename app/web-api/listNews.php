<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/News.php';


        $db = new db();

    /*** REQUIRE USER AUTHENTICATION ***/
        $db->perf("requireEditorRole");
    $requireEditorRole = false;
        $db->perf("SaronUser");
    $saronUser = new SaronUser(wp_get_current_user());    

        $db->perf("isPermitted");

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
        exit();
    }

    try{
        $db->perf("Start list news");
        $news = new News($db, $saronUser);
        $result = $news->select();    
        $db->perf("Stop list news");
        $db->dispose();
        echo $result;        
            $db->perf("echo News");

    }
    catch(Exception $error){
        echo $error->getMessage();        
        $db->dispose();
    }