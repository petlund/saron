<?php

/**
 * Description of Nonce
 *
 * @author peter
 */
class Nonce {
    //put your code here
private $nonce = "";
        
    function __construct($db, $saronUser) {
        try{
            $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE);
            $this->nonce = random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX) . random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX);
        }
        CATCH(Exception $e){
            return;
        }
    }

    
    function getCSPNonce(){
        return "'nonce-" . $this->nonce . "'";
    }

    
    function getScriptNonce(){
        return $this->nonce;

    }
}