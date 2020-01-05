<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/Person.php';

    $requireEditorRole = false;
    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
        exit();
    }

    try{
        $db = new db();
        $person = new Person($db, $saronUser);
        $result = $person->select();        
        $db->dispose();
        echo $result;
    }
    catch(Exception $error){
        echo $error->getMessage();        
        $db->dispose();            
    }

