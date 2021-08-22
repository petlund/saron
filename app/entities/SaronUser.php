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
require_once SARON_ROOT . 'app/access/SaronCookie.php'; 
require_once SARON_ROOT . "app/access/Ticket.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';

class SaronUser{
    private $db;
    private $editor;
    private $org_editor;
    private $userDisplayName;
    private $timeStamp;
    private $ticket;
    public $WP_ID;
    
    
    function __construct($db) {
        $this->db = $db;

        $this->ticket = getTicketFromCookie();

        $attributes = $this->load($this->ticket);        

        $this->editor = $attributes[0]["Editor"];
        $this->org_editor = $attributes[0]["Org_Editor"];
        $this->userDisplayName = $attributes[0]["UserDisplayName"];
        $this->WP_ID = $attributes[0]["WP_ID"];
        $this->timeStamp = $attributes[0]["Time_Stamp"];
    }

    
    
    public function hasValidSaronSession($requireEditor=0, $requireOrg=0, $checkTicketRenewalTime=false){
        try{
            $ticket = getTicketFromCookie();
            
            if(strlen($ticket) === 0){  
                throw new Exception($this->getErrorMessage("(9) Your session is out of scope. " . $ex));
            }
                        
            $this->checkTicket($ticket, $requireEditor, $requireOrg);
            if($checkTicketRenewalTime){
                if($this->isItTimeToReNewTicket($ticket)){
                    $newTicket = $this->renewTicket($ticket);
                    setSaronCookie($newTicket);
                }
            }
            return true;
        }
        catch(Exception $ex){
            throw new Exception($this->getErrorMessage("(8) Your session is out of scope. " . $ex));
        }
    }
    
    
    
    public function isEditor(){
        if($this->editor === '1'){
            return true;
        }
        return false;
    }

    
    
    public function isOrgEditor(){
        if($this->org_editor === '1'){
            return true;
        }
        return false;
    }
    
    
    
    public function getDisplayName(){
        return $this->userDisplayName ;
    }
    


    public function getRole(){
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
    

    
    public function getRoleSql($continue){
        $SQL_ALIAS = ' as user_role';
            
        $sql = "'" . $this->getRole() . "'" . $SQL_ALIAS;
            
        if($continue){
            return $sql . ", ";
        }
        else{
            return $sql . " ";            
        }
    }
    

    
    public function getRoleDisplayName(){
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
    
        
    
    public function getNameAndRole(){
        return $this->getDisplayName() . " - " . $this->getRoleDisplayName();
    }
    
    
    
    public function select(){
        $select = "SELECT Time_Stamp ";
        $from = "FROM SaronUser ";
        $where = "WHERE AccessTicket = '" . $this->ticket. "' ";
        $result = $this->db->select($this, $select, $from, $where, "", "", RECORD);
        return $result;
    }


    
    public function update(){

        if($this->db->fieldValueExist($this->WP_ID, -1, "WP_ID", "SaronUser")){
            return $this->db->update("Update SaronUser ", "Set Last_Activity = Now() ", "where WP_ID=" . $this->WP_ID);
        }
        else{
            throw new Exception($this->getErrorMessage("(7) Your session is out of scope. " . $ex));
            
        }
    }
    
    
    
    private function cleanSaronUser($wp_id){
        $sql = "DELETE from SaronUser "
                . "where " . NOW_LAST_ACTIVITY_DIFF. " > " . SESSION_EXPIRES . " "
                . "OR " . NOW_TIME_STAMP_DIFF . " > " . COOCKIE_EXPIRES . " "
                . "OR WP_ID=" . $wp_id;
        
        $this->db->delete($sql);
    }
 
    
    
    private function load($ticket){
        $this->cleanSaronUser(-1);   

        $sql = "select * from  SaronUser where "
                . "AccessTicket='" . $ticket . "'"
                . "AND " . NOW_LAST_ACTIVITY_DIFF . " < " . SESSION_EXPIRES . " "
                . "AND " . NOW_TIME_STAMP_DIFF. " < " . COOCKIE_EXPIRES;        
        
        $attributes = $this->db->sqlQuery($sql);
        
        if(count($attributes) === 0){
            throw new exception($this->getErrorMessage("You are not authorized for this service"));
        }
            
        return $attributes;       
    }

    
    
    private function getErrorMessage($msg){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = $msg;
        return json_encode($error);
    }
    
    
    
    private function isItTimeToReNewTicket($ticket){
        $sql = "select if(" . NOW_TIME_STAMP_DIFF . " > " . TICKET_RENEWIAL_PERIOD_IN_SEC . ","
                . "if(" . NOW_LAST_ACTIVITY_DIFF . " > " . SESSION_EXPIRES . ", -1, 1)"
                . ",0) as Answer "
                . "from SaronUser Where AccessTicket = '" . $ticket ."'";

        $result = $this->db->sqlQuery($sql);

        $answer=0;
        foreach($result as $aRow){
            $answer = $aRow["Answer"];
        }

        if($answer === '1'){
            return true;
        }
        else if($answer === '-1'){ // Not relevant with the new sql statement
            throw new Exception($this->getErrorMessage("(6) Your session is out of scope. " . $ex));
        }
        return false;  // It is´t time yet          

    } 
    
    
    
    private function checkTicket($ticket, $editor, $org_editor){
        if(strlen($ticket) === 0){
            throw new Exception($this->getErrorMessage("(5) Your session is out of scope. " . $ex));
        }
        
        $this->cleanSaronUser(-1);
        
        $sql = "Select count(*) as c FROM SaronUser "
        . "WHERE AccessTicket = '" . $ticket . "' "
        . "AND " . NOW_LAST_ACTIVITY_DIFF . " < " . SESSION_EXPIRES . " "
        . "AND " . NOW_TIME_STAMP_DIFF. " < " . COOCKIE_EXPIRES . " "       
        . "AND Editor >= " . $editor . " "
        . "AND (Org_Editor >= " . $org_editor . " OR Editor >= " . $editor . ")";
    
        if(!$listResult = $this->db->sqlQuery($sql)){
            $this->php_dev_error_log("Exception in exist function", $sql);
            throw new Exception($this->getErrorMessage("(4) Your session is out of scope. " . $ex));
        }
        
        $countRows = $listResult[0]["c"];
        
        if($countRows === '1'){
            return true;
        }
        throw new Exception($this->getErrorMessage("(3) Your session is out of scope. " . $ex));
    }
    
    
    
    
    private function renewTicket($oldTicket){
        try{
            $this->db->transaction_begin();
            $sql = "Select Id from SaronUser where "
                    . "AccessTicket = '" . $oldTicket . "'"
                    . "AND " . NOW_LAST_ACTIVITY_DIFF . " < " . SESSION_EXPIRES . " "
                    . "AND " . NOW_TIME_STAMP_DIFF. " < " . COOCKIE_EXPIRES;

            $result1 = $this->db->sqlQuery($sql);
            
            $id;
            if($result1){
                foreach($result1 as $aRow){
                    $id = $aRow["Id"];
                }
            }
            else{
                throw new Exception($this->getErrorMessage("(2) Your session is out of scope. " . $ex));
            }

            $update = "update SaronUser ";
            $set = "SET ";        
            $set.= "AccessTicket = " . getAccessTicket() . ", ";
            $set.= "Time_Stamp = Now() ";
            $where = "WHERE AccessTicket = '" . $oldTicket . "'";

            $this->db->update($update, $set, $where);
            
            $result2 = $this->db->sqlQuery("Select AccessTicket from SaronUser where Id = " . $id);
    
            $ticket = "";
            foreach($result2 as $aRow){
                $ticket = $aRow["AccessTicket"];
            }
            $this->db->transaction_end();

            return $ticket;
        }
        catch(Exception $ex){
            $this->db->transaction_roll_back();
            $this->db->transaction_end();
            throw new Exception($this->getErrorMessage("(1) Your session is out of scope. " . $ex));
        }
    }
}
