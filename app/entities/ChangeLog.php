<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class ChangeLog extends SuperEntity{
    private $uid;
    private $cid;
    
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        $this->uid = (String)filter_input(INPUT_POST, "uid", FILTER_SANITIZE_STRING);    
        $this->cid = (String)filter_input(INPUT_POST, "cid", FILTER_SANITIZE_STRING);    
        
    }
    
    function select($id = -1){
        switch ($this->resultType){
        case OPTIONS:
            switch ($this->field){
            case "User":
                return $this->selectFieldAsOptions($this->field);       
            case "ChangeType":
                return $this->selectFieldAsOptions($this->field);       
            }
            default:
                return $this->selectRecords($id = -1); 
        }
    }
    
    
    
    function selectRecords($id = -1){
        $select = "SELECT Id, ChangeType, User,  " . DECRYPTED_BUSINESS_KEY . ", " . DECRYPTED_DESCRIPTION . ", Inserter, Inserted, InserterName, " . $this->saronUser->getRoleSql(false) . " ";
        if($id < 0){
            $where = "WHERE User like '%" . $this->uid . "%' AND  ChangeType like '%" . $this->cid . "%' "; 
            $result = $this->db->select($this->saronUser, $select , "FROM Changes ", $where, $this->getSortSql(), $this->getPageSizeSql(), RECORDS);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM Changes ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }
            
    
    
    function selectFieldAsOptions($field){
        $select = "select '' as Value, 'Alla' as DisplayText ";
        $select.= "Union ";
        $select.= "select " . $field . " as Value, " . $field . " as DisplayText ";

        $from = "FROM Changes ";
        
        $where = "";
        
        $groupBy ="Group by " . $field . " ORDER BY DisplayText ";
        
        $result = $this->db->select($this->saronUser, $select, $from, $where, $groupBy, "", "Options");    
        return $result;
    }     

}
