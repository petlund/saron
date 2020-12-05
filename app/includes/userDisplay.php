<?php
require_once "config.php";
require_once SARON_ROOT . 'app/database/db.php';
    try{
        $db = new db();
        $saronUser = new SaronUser($db);
        echo "Inloggad som " . $saronUser->getNameAndRole(); 
    }
    catch(Exception $error){
        throw new Exception($error);
    }