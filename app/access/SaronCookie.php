<?php
require_once "config.php";
require_once SARON_ROOT . 'app/database/db.php';

    // 
    function setSaronCookie($ticket){

        $arr_cookie_options = array (
            'expires' => time() + COOCKIE_EXPIRES, 
            'path' => COOCKIE_PATH, 
            'domain' =>  COOCKIE_DOMAIN, 
            'secure' =>  COOCKIE_SECURE,
            'httponly' =>  COOCKIE_HTTP_ONLY, 
            'samesite' =>  COOCKIE_SAMESITE, 
            );

        
        if(strlen($ticket) === 0){
            $ticket = 'COOKIE_NOT_VALID';
        }
        
        setcookie(COOKIE_NAME, $ticket, $arr_cookie_options);  

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache"); //HTTP 1.0
        header("Expires: 0");
    }


    
    function removeSaronCookie(){
        if(isset($_COOKIE[COOKIE_NAME])) {
            setcookie(COOKIE_NAME, "NOT_VALID", time() - 3600);            
        }        
    }



    function getTicketFromCookie(){
        if(isset($_COOKIE[COOKIE_NAME])) {
            return (String)filter_input(INPUT_COOKIE, COOKIE_NAME, FILTER_SANITIZE_STRING);                
        }
        return "";
    }

    
    
