<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SaronUser
 *
 * @author peter
 */

require_once "config.php";
class SaronMetaUser{
    public $WP_ID;
    public $userDisplayName;
    
    
    function __construct($WP_ID=-1, $userDisplayName='System') {
        $this->WP_ID = $WP_ID;
        $this->userDisplayName = $userDisplayName;
    }    
}
