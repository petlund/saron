<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationUnit extends SuperEntity{
    
    private $id;
    private $name;
    private $description;
    private $subUnitEnabled;
    private $posEnabled;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->subUnitEnabled = (int)filter_input(INPUT_POST, "SubUnitEnabled", FILTER_SANITIZE_NUMBER_INT);
        $this->posEnabled = (int)filter_input(INPUT_POST, "PosEnabled", FILTER_SANITIZE_NUMBER_INT);
    }

    function checkUnittData(){
        $error = array();

        if(strlen(trim($this->name)) === 0){
            $error["Result"] = "ERROR";
            $error["Message"] = "Det saknas ett namn på den organisatoriska enheten";
            throw new Exception(json_encode($error));
        }
        if($this->db->fieldValueExist($this->name, $this->id, "Name", "Org_UnitType")){
            $error["Result"] = "ERROR";
            $error["Message"] = "Det finns redan en organisatorisk enhet med namnet: '" . $this->name . "'";
            throw new Exception(json_encode($error));
        }
    }

 

    function select($id = -1, $rec=RECORDS){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
        default:
            return $this->selectDefault($id, $rec);
        }
    }

    
    function selectDefault($id = -1, $rec=RECORDS){
        $select = "SELECT Typ.Id, Typ.PosEnabled, Typ.SubUnitEnabled, Typ.Name, Typ.Description, Typ.Updater, Typ.Updated, "; 
        $select.= "(Select count(*) from `Org_Role-UnitType` as UnitRole WHERE UnitRole.OrgUnitType_FK = Typ.Id) as HasPos, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_UnitType as Typ ";
        if($this->posEnabled > 0){
            $from = "FROM Org_UnitType as Typ inner join `Org_Role-UnitType` as Rut on Rut.OrgUnitType_FK = Typ.Id ";
            $where = "WHERE OrgRole_FK = " . $this->posEnabled . " ";
        }
        else{
            $where = "";
        }
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM Org_UnitType as Typ ", "WHERE typ.id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), $rec);        
            return $result;
        }
    }

    function selectOptions(){
//        $sql = "SELECT 0 as Value, ' Topp' as DisplayText "; 
//        $sql.= "Union "; 
        $select = "SELECT id as Value, Name as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM Org_UnitType ", "", "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function insert(){
        $this->checkUnittData();
        $sqlInsert = "INSERT INTO Org_UnitType (Name, Description, PosEnabled, SubUnitEnabled, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->name . "', ";
        $sqlInsert.= "'" . $this->description . "', ";
        $sqlInsert.= "'" . $this->posEnabled . "', ";
        $sqlInsert.= "'" . $this->subUnitEnabled . "', ";
        $sqlInsert.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_UnitType", "Id");
        return $this->select($id, RECORD);
    }
    
    
    function update(){
        $this->checkUnittData();
        $update = "UPDATE Org_UnitType ";
        $set = "SET ";        
        $set.= "Name='" . $this->name . "', ";        
        $set.= "PosEnabled='" . $this->posEnabled . "', ";        
        $set.= "SubUnitEnabled='" . $this->subUnitEnabled . "', ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "Updater='" . $this->saronUser->ID . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from Org_UnitType where Id=" . $this->id);
    }
}
