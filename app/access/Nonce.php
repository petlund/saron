<?php

/**
 * Description of Nonce
 *
 * @author peter
 */
class Nonce {
    //put your code here
    private $db;
    
    private $nonce = "";
    
    function __construct($db, $saronUser) {        
        try{
            $this->db = $db;
            // nonce should not depend on active session 
//            $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE);
            $this->nonce = random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX) . random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX);
        }
        CATCH(Exception $e){
            $db->saron_dev_log(LOG_ERR, 'Nonce',  '__construct', $e, null);
        }
    }

    
    function getCSPNonce(){
        return "'nonce-" . $this->nonce . "'";
    }

    
    function getScriptNonce(){
        return $this->nonce;

    }
}