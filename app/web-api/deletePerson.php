<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/SaronCookie.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/Person.php';

    $requireEditorRole = 1;
    $requireOrg = 0;    

    try{
        $db = new db();
        $saronUser = new SaronUser($db, $requireEditorRole, $requireOrg);
        $person = new Person($db, $saronUser);
        $db->transaction_begin();
        $result = $person->anonymization();
        $db->transaction_end();
        
        echo $result;
    }
    catch(Exeption $error){
        $db->transaction_roll_back();
        $db->transaction_end();
        echo $error->getMessage();        
                    
    }    