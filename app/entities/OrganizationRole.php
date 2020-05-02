<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationRole extends SuperEntity{
    
    private $id;
    private $name;
    private $description;
    private $isRole;
    private $hasSubUnit;
    private $orgId;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->businessUnitTree_FK = (int)filter_input(INPUT_POST, "BusinessUnitTree_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->businessRole_FK = (int)filter_input(INPUT_POST, "BusinessRole_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->businessPosStatus_FK = (int)filter_input(INPUT_POST, "BusinessPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->personId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        $this->orgId = (int)filter_input(INPUT_GET, "OrgId", FILTER_SANITIZE_NUMBER_INT);
        $this->prePersonId = (int)filter_input(INPUT_POST, "PrePersonId", FILTER_SANITIZE_NUMBER_INT);
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
        $from = "FROM BusinessRole as Role left outer join BusinessUnitRole as UnitRole on BusinessRole_FK=Role.Id ";
        if($this->orgId >= 0){
            $where = "WHERE BusinessUnit_FK = " . $this->orgId . " ";
        }
        else{
            $where = "";
        }
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM BusinessRole as Role ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), $rec);        
            return $result;
        }
    }

    function selectOptions(){
//        $sql = "SELECT 0 as Value, ' Topp' as DisplayText "; 
//        $sql.= "Union "; 
        $select = "SELECT id as Value, Name as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM BusinessRole ", "", "Order by DisplayText ", "", "Options");    
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
