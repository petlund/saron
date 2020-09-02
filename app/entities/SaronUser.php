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

class SaronUser {
    private $NO_ROLE = "NO_ROLE";
    private $user;
    public $ID;
    function __construct($user) {
        $this->user = $user;
        $this->ID = $user->ID;
    }
    
       
    function isSaronUser(){
        /*** Check if the user had a saron role ***/
        if($this->getRole() !== $this->NO_ROLE){ 
            $otp = $this->user->get("wp-otp");
            if($otp["enabled"] || TEST_ENV){ // In test environment OTP enabeled account is not necessary.
                return true;
            }
        }         
        return false;
    }
    
    
    function isEditor(){
        /*** Check if the user had an editor role ***/
        for($i = 0; $i < count($this->user->roles); $i++){
            if(strpos($this->user->roles[$i],  SARON_ROLE_PREFIX . SARON_ROLE_EDITOR) !== FALSE){ // CHECK IF THE USER IS A MEMBER OF THE GROUP  (test)saron_edit
                return true;
            }
        } 
        return false;
    }

    
    
    function getDisplayName(){
        return $this->user->user_firstname . " " . $this->user->user_lastname ;
    }
    

    
    function getRole(){
        for($i = 0; $i < count($this->user->roles); $i++){
            if($this->user->roles[$i]===SARON_ROLE_PREFIX . SARON_ROLE_EDITOR){
                return SARON_ROLE_EDITOR;
            }
        }
        for($i = 0; $i < count($this->user->roles); $i++){
            if($this->user->roles[$i]===SARON_ROLE_PREFIX . SARON_ROLE_VIEWER){
                return SARON_ROLE_VIEWER;                
            }
        }   
        return $this->NO_ROLE;
    }
    

    function getRoleSql($continue){
        $SQL_ALIAS = ' as user_role';
            
        $sql = "'" . $this->getRole() . "'" . $SQL_ALIAS;
            
        if($continue){
            return $sql . ", ";
        }
        else{
            return $sql . " ";            
        }
    }
    

    
    function getRoleDisplayName(){
        for($i = 0; $i < count($this->user->roles); $i++){
            if($this->user->roles[$i]==SARON_ROLE_PREFIX . SARON_ROLE_EDITOR){
                return SARON_DISPLAY_NAME_EDITOR;
            }
        }
        for($i = 0; $i < count($this->user->roles); $i++){
            if($this->user->roles[$i]==SARON_ROLE_PREFIX . SARON_ROLE_VIEWER){
                return SARON_DISPLAY_NAME_VIEWER;                
            }
        }   
        return $this->NO_ROLE;
    }
    
    
    
    function getNameAndRole(){
        return $this->getDisplayName() . " - " . $this->getRoleDisplayName();
    }
}
