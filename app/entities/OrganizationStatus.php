<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationStatus extends SuperEntity{
    
    private $id;
    private $name;
    private $description;
    private $sortOrder;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->statusfilter = (String)filter_input(INPUT_GET, "statusfilter", FILTER_SANITIZE_STRING);
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->sortOrder = (String)filter_input(INPUT_POST, "SortOrder", FILTER_SANITIZE_STRING);
    }


    function select($Id = -1, $rec = RECORDS){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
        default:
            return $this->selectDefault($Id, $rec);
        }   
    }

    
    function selectDefault($id = -1, $rec=RECORDS){
        $select = "SELECT *, " . $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_PosStatus ";
        if($this->id > 0){
            $where = "WHERE Id = " . $this->id . " ";
        }
        else{
            $where = "";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    function selectOptions(){
        $select = "SELECT Id as Value, Name as DisplayText ";
        
        $where = "";
        switch ($this->statusfilter) {
        case "engagement":
            $where = "WHERE Id < 3 "; // only "Avstämd" and "Förslag"
            break;
        default:
            $where = "";
        }
        
        $result = $this->db->select($this->saronUser, $select , "FROM Org_PosStatus ", $where, "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    
    function update(){
        $update = "UPDATE Org_PosStatus ";
        $set = "SET ";        
        $set.= "Name='" . $this->name . "', ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "SortOrder='" . $this->sortOrder . "', ";        
        $set.= "Updater='" . $this->saronUser->ID . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from Org_UnitType where Id=" . $this->id);
    }
}
