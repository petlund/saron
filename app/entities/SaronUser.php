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
require_once SARON_ROOT . 'app/entities/SaronMetaUser.php'; 
require_once SARON_ROOT . 'app/access/SaronCookie.php'; 
require_once SARON_ROOT . "app/access/Ticket.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';

class SaronUser extends SaronMetaUser{
    private $db;
    private $editor;
    private $org_editor;
    private $timeStamp;
    private $ticket;
    private $sessionOK;
    public $WP_ID;
    public $userDisplayName;
    
    
    function __construct($db) {
        $this->db = $db;

        $this->ticket = getTicketFromCookie();
        try{
            $attributes = $this->load($this->ticket);        

            $this->editor = $attributes[0]["Editor"];
            $this->org_editor = $attributes[0]["Org_Editor"];
            $this->userDisplayName = $attributes[0]["UserDisplayName"];
            $this->WP_ID = $attributes[0]["WP_ID"];
            $this->timeStamp = $attributes[0]["Time_Stamp"];

            parent::__construct($this->WP_ID, $this->userDisplayName);
}
        catch(Exception $error){
            error_log($error . "\n\n");
        }
    }

    
    
    public function hasValidSaronSession($requireEditor=0, $requireOrg=0, $checkTicketRenewalTime=false){
        try{
            $ticket = getTicketFromCookie();
            
            if(strlen($ticket) === 0){  
                throw new Exception($this->getErrorMessage("(9) Your session is out of scope. "));
            }
                        
            $this->checkTicket($ticket, $requireEditor, $requireOrg);
            if($checkTicketRenewalTime){
                if($this->isItTimeToReNewTicket($ticket)){
                    $newTicket = $this->renewTicket($ticket);
                    setSaronCookie($newTicket);
                    $this->db->saron_dev_log(LOG_INFO, "SaronUser", "hasValidSaronSession", "Yes! " . getTicketFromCookie(), "");
                }
            }
            $this->sessionOK=true;
            return true;
        }
        catch(Exception $ex){
            $this->sessionOK=false;
            $this->db->saron_dev_log(LOG_INFO, "SaronUser", "hasValidSaronSession", "No! " . getTicketFromCookie(), "");
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
            return $this->db->update("Update SaronUser ", "Set Last_Activity = Now() ", "where WP_ID=" . $this->WP_ID, 'SaronUser', 'WP_ID', $this->WP_ID, 'Användare', 'Användarnamn', null,null, false);
        }
        else{
            throw new Exception($this->getErrorMessage("(7) Your session is out of scope. " ));
            
        }
    }
    
    
    
    private function cleanSaronUser($wp_id){
        $sql = "DELETE from SaronUser "
                . "where " . NOW_LAST_ACTIVITY_DIFF. " > " . SESSION_EXPIRES . " "
                . "OR " . NOW_TIME_STAMP_DIFF . " > " . COOCKIE_EXPIRES . " "
                . "OR WP_ID=" . $wp_id;
        $user = new SaronMetaUser();
        $this->db->delete($sql, 'SaronUser', 'id', $wp_id, 'Användarsession', 'Användarnamn','Bortstädning av gamla sessioner', $user, false);
    }
 
    
    
    private function load($ticket){
        $this->cleanSaronUser(-1);   

        $sql = "select * from  SaronUser where "
                . "AccessTicket='" . $ticket . "' "
                . "AND " . NOW_LAST_ACTIVITY_DIFF . " < " . SESSION_EXPIRES . " "
                . "AND " . NOW_TIME_STAMP_DIFF. " < " . COOCKIE_EXPIRES;        
        
        $attributes = $this->db->sqlQuery($sql);
        
        if(count($attributes) === 0){
            throw new exception($this->getErrorMessage("You are not authorized for this service: " . var_dump($attributes)));
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
                . "from SaronUser Where AccessTicket = '" . $ticket . "' ";

        $result = $this->db->sqlQuery($sql);

        $answer=0;
        foreach($result as $aRow){
            $answer = $aRow["Answer"];
        }

        if($answer === '1'){
            return true;
        }
        else if($answer === '-1'){ // Not relevant with the new sql statement
            throw new Exception($this->getErrorMessage("(6) Your session is out of scope. "));
        }
        return false;  // It is´t time yet          

    } 
    
    
    
    private function checkTicket($ticket, $editor, $org_editor){
        if(strlen($ticket) === 0){
            throw new Exception($this->getErrorMessage("(5) Your session is out of scope. "));
        }
        
        $this->cleanSaronUser(-1);
        
        $sql = "Select count(*) as c FROM SaronUser "
        . "WHERE AccessTicket = '" . $ticket . "' "
        . "AND " . NOW_LAST_ACTIVITY_DIFF . " < " . SESSION_EXPIRES . " "
        . "AND " . NOW_TIME_STAMP_DIFF. " < " . COOCKIE_EXPIRES . " "       
        . "AND Editor >= " . $editor . " "
        . "AND (Org_Editor >= " . $org_editor . " OR Editor >= " . $editor . ")";
    
        if(!$listResult = $this->db->sqlQuery($sql)){
            $this->db->saron_dev_log(LOG_ERR, "SaronUser", "checkTicket", "Exception", $sql);
            throw new Exception($this->getErrorMessage("(4) Your session is out of scope. "));
        }
        
        $countRows = $listResult[0]["c"];
        
        if($countRows === '1'){
            return true;
        }
        throw new Exception($this->getErrorMessage("(3) Your session is out of scope. "));
    }
    
    
    
    
    private function renewTicket($oldTicket){
        try{
            $this->db->transaction_begin();
            $sql = "Select Id from SaronUser where "
                    . "AccessTicket = '" . $oldTicket . "' "
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
                throw new Exception($this->getErrorMessage("(2) Your session is out of scope. "));
            }

            $update = "update SaronUser ";
            $set = "SET ";        
            $set.= "AccessTicket = " . getAccessTicket() . ", ";
            $set.= "Time_Stamp = Now() ";
            $where = "WHERE AccessTicket = '" . $oldTicket . "'";

            $this->db->update($update, $set, $where, 'SaronUser', 'WP_ID', -1, 'Changes', '', 'Ticket renewal', $this, false);
            $result2 = $this->db->sqlQuery("Select AccessTicket from SaronUser where Id = " . $id);
    
            $ticket = "";
            foreach($result2 as $aRow){
                $ticket = $aRow["AccessTicket"];
            }
            $this->db->saron_dev_log(LOG_INFO, "saronUser","renewTicket", "TICKET: " . $ticket, null);
            $this->db->transaction_end();

            return $ticket;
        }
        catch(Exception $ex){
            $this->db->transaction_roll_back();
            $this->db->transaction_end();
            throw new Exception($this->getErrorMessage("(1) Your session is out of scope. " . $ex));
        }
    }
    
    
    function getLoginHeadLine(){
        echo "Inloggad som " . $this->getNameAndRole(); 
    }
    
    
    
    function getDBConnectHeadLine(){
    
        $headline =  "Databas: " . DATABASE;
        if($this->sessionOK){
            $headline.= " - Anslutning  OK!";
            echo $headline;
        }
        else{
            $headline.= " - Anslutning  ERROR!";
            echo $headline;
        }

    }
}
