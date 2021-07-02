<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/News.php';


    try{
        $db = new db();
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE);
        $news = new News($db, $saronUser);
        $result = $news->select();    
        echo $result;        
    }
    catch(Exception $error){
        echo $error->getMessage();        
    }