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
    
    
   function select($id = -1, $rec = RECORDS){
        switch ($this->selection){
        case "options":
            return $this->selectAsOptions();       
        default:
            return $this->selectDefault($id, $rec);
        }
    }
    
    
    function selectDefault($id = -1, $rec=RECORDS){
        $select = "SELECT *, " . $this->saronUser->getRoleSql(false) . " ";
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "", $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), $rec);        
            return $result;
        }
    }
    
    
    function selectAsOptions(){
        $select = "Select Id as Value, Name as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "", $this->getSortSql(), $this->getPageSizeSql(), OPTIONS);    
        return $result;        
    }
    

//    function insert(){
//        $sqlInsert = "INSERT INTO News (information, writer) ";
//        $sqlInsert.= "VALUES (";
//        $sqlInsert.= "'" . $this->information . "', ";
//        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "')";
//        
//        $id = $this->db->insert($sqlInsert, "News", "id");
//        $result =  $this->select($id, RECORD);
//        return $result;
//    }
    
    
    function update(){
        $update = "UPDATE MemberState ";
        $set = "SET ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "FilterCreate=" . $this->filterCreate . ", ";        
        $set.= "FilterUpdate=" . $this->filterUpdate . ", ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "', ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "' ";        
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

//    function delete(){
//        return $this->db->delete("delete from News where id=" . $this->id);
//    }
}