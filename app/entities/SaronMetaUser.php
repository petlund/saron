<?php


/**
 * Description of SaronUser
 *
 * @author peter
 */

require_once "config.php";
class SaronMetaUser{
    private $WP_ID;
    private $userDisplayName;
    private $user_login;
    
    
    function __construct($WP_ID=-1, $userDisplayName='System', $user_login='system') {
        $this->WP_ID = $WP_ID;
        $this->userDisplayName = $userDisplayName;
        $this->user_login = $user_login;
    }    


    public function getWP_ID(){
        return $this->WP_ID ;
    }
    
    
    public function getUserName(){
        return $this->user_login ;
    }
    
    
    
    public function getDisplayName(){
        return $this->userDisplayName ;
    }        
}
