<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class MemberState extends SuperEntity{
    
    private $description;
    private $filterUpdate;
    private $filterCreate;
            
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->filterUpdate = (int)filter_input(INPUT_POST, "FilterUpdate", FILTER_SANITIZE_NUMBER_INT);
        $this->filterCreate = (int)filter_input(INPUT_POST, "FilterCreate", FILTER_SANITIZE_NUMBER_INT);
    }
    
    
   function select($id = -1){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectAsOptions();       
        default:
            return $this->selectDefault($id);
        }
    }
    
    
    function selectDefault($id){
        $select = "SELECT *, " . $this->saronUser->getRoleSql(false) . " ";
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "", $this->getSortSql(), $this->getPageSizeSql(), RECORDS);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "WHERE Id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }
    
    
    function selectAsOptions(){
        $select = "Select Id as Value, Name as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "", $this->getSortSql(), $this->getPageSizeSql(), OPTIONS);    
        return $result;        
    }
    

    
    function update(){
        $update = "UPDATE MemberState ";
        $set = "SET ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "FilterCreate=" . $this->filterCreate . ", ";        
        $set.= "FilterUpdate=" . $this->filterUpdate . ", ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "', ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "' ";        
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }



    function delete(){
    }
}