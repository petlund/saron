<?php
require_once "config.php";

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


    function hasValidSaronSession($requireEditor=0, $requireOrg=0){
        try{
                $ticket = getTicketFromCookie();
            
            if(strlen($ticket) === 0){  
                throw new Exception();
            }
            
            $db = new db();
            $db->checkTicket($ticket, 0, 0);
            if($db->isItTimeToReNewTicket($ticket)){
                $newTicket = $db->renewTicket($ticket);
                    setSaronCookie($newTicket);
            }
            return true;
        }
        catch(Exception $ex){
            header("Location: /" . SARON_URI . LOGOUT_URI);
            return false;
        }
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
        return "NOT_VALID";
    }
