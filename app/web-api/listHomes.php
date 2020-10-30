<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/Home.php';

    //header_remove(); 

    $requireEditorRole = false;
    $requireOrg = false;
    
    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole, $requireOrg)){
        echo notPermittedMessage();
        exit();
    }


    try{
        $db = new db();
        $homes = new Homes($db, $saronUser);
        $result = $homes->select();    
        $db->dispose();
        echo $result;
    }
    catch(Exception $error){
        $db->dispose();
        echo $error->getMessage();
    }