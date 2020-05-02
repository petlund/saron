<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationStatus extends SuperEntity{
    
    private $id;
    private $name;
    private $description;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
    }


    function select($Id = -1){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
        default:
            return $this->selectDefault($Id);
        }   
    }

    
    function selectDefault($id = -1, $rec="Records"){
        $select = "SELECT *, " . $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM BusinessPosStatus ";
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
        $select = "SELECT id as Value, Name as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM BusinessPosStatus ", "", "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function insert(){
        $sqlInsert = "INSERT INTO BusinessUnitType (Name, IsRole, HasSubUnit, Description, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->name . "', ";
        $sqlInsert.= "'" . $this->isRole . "', ";
        $sqlInsert.= "'" . $this->hasSubUnit . "', ";
        $sqlInsert.= "'" . $this->description . "', ";
        $sqlInsert.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert, "BusinessUnitType", "Id");
        return $this->select($id, "Record");
    }
    
    
    function update(){
        $update = "UPDATE BusinessUnitType ";
        $set = "SET ";        
        $set.= "Name='" . $this->name . "', ";        
        $set.= "IsRole='" . $this->isRole . "', ";        
        $set.= "HasSubUnit='" . $this->hasSubUnit . "', ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "Updater='" . $this->saronUser->ID . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }

    function delete(){
        return $this->db->delete("delete from BusinessUnitType where Id=" . $this->id);
    }
}
