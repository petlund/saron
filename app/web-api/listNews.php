<?php
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/SaronCookie.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/News.php';


    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = 0;
    $requireOrg = 0;

    try{
        $db = new db();
        $saronUser = new SaronUser($db, $requireEditorRole, $requireOrg);
        $news = new News($db, $saronUser);
        $result = $news->select();    
        echo $result;        
    }
    catch(Exception $error){
        echo $error->getMessage();        
    }