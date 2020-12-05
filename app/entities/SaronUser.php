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
require_once SARON_ROOT . 'app/access/cookie.php'; 
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';

class SaronUser {
    private $editor;
    private $org_editor;
    private $userDisplayName;
    public $WP_ID;
    
    
    function __construct($db, $requireEditor=0, $requireOrg=0) {
        try{
            if(hasValidSaronSession()){
                $ticket = getTicketFromCookie();
                $db->checkTicket($ticket, $requireEditor, $requireOrg);
                $attributes = $db->loadSaronUser($ticket);        

                $this->editor = $attributes[0]["Editor"];
                $this->org_editor = $attributes[0]["Org_Editor"];
                $this->userDisplayName = $attributes[0]["UserDisplayName"];
                $this->WP_ID = $attributes[0]["WP_ID"];
            }
            else{
                throw new Exception();
            }
        }
        catch(Exception $ex){
            $error=array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Du är inte behörig till den här funktionen.";
            throw new Exception(json_encode($error));
        }
    }
       

    function isEditor(){
        if($this->editor === '1'){
            return true;
        }
        return false;
    }

    
    
    function isOrgEditor(){
        if($this->org_editor === '1'){
            return true;
        }
        return false;
    }
    
    
    
    function getDisplayName(){
        return $this->userDisplayName ;
    }
    


    function getRole(){
        if($this->isEditor()){
            return SARON_ROLE_EDITOR;
        }
        else if($this->isOrgEditor()){
            return SARON_ROLE_ORG;
        }
        else{
            return SARON_ROLE_VIEWER;                
        }
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
        if($this->isEditor()){
            return SARON_DISPLAY_NAME_EDITOR;
        }
        else if($this->isOrgEditor()){
            return SARON_DISPLAY_NAME_ORG;
        }
        else{
            return SARON_DISPLAY_NAME_VIEWER;                
        }
    }
    
        
    function getNameAndRole(){
        return $this->getDisplayName() . " - " . $this->getRoleDisplayName();
    }
}
