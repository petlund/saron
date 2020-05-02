<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationPos extends SuperEntity{
    
    private $nodeId;
    private $id;
    private $businessPosRole_FK;
    private $businessPosStatus_FK;
    private $personId;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->nodeId = (int)filter_input(INPUT_GET, "NodeId", FILTER_SANITIZE_NUMBER_INT);
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->businessPosRole_FK = (int)filter_input(INPUT_POST, "BusinessPosRole_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->businessPosStatus_FK = (int)filter_input(INPUT_POST, "BusinessPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->personId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
    }


    function select($id = -1, $rec = "Records"){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
        default:
            return $this->selectDefault($id, $rec);
        }
    }

    
    function selectDefault($id = -1, $rec="Records"){
        $select = "SELECT Pos.*, Role.*, ";
        $select.= getPersonSql("pPrev", "PrevPerson", true);
        $select.= "Role.Name as RoleName, ";
        $select.= getMemberStateSql("pCur", "MemberState", true);
        $select.= getFieldSql("pCur", "Email", "EmailEncrypt", "", true, true);
        $select.= getFieldSql("pCur", "Mobile", "MobileEncrypt", "", true, true);
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "FROM BusinessPos as Pos inner join BusinessRole Role on Pos.BusinessPosRole_FK = Role.Id ";
        $from.= "left outer join People as pCur on pCur.Id=Pos.PersonId ";
        $from.= "left outer join People as pPrev on pPrev.Id=Pos.PrevPersonId ";
        
        if($id < 0){
            $where = "";
        }
        else{
            $where = "WHERE Pos.Id = " . $id . " ";
        }
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }

    function selectOptions(){
        $select = "SELECT id as Value, Name as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM BusinessUnitType ", "", "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function insert(){
        $sqlInsert = "INSERT INTO BusinessPos (BusinessUnitTree_FK, BusinessPosRole_FK, BusinessPosStatus_FK, PersonId, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->nodeId . "', ";
        $sqlInsert.= "'" . $this->businessPosRole_FK . "', ";
        $sqlInsert.= "'" . $this->businessPosStatus_FK . "', ";
        $sqlInsert.= "'" . $this->personId . "', ";
        $sqlInsert.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert, "BusinessPos", "Id");
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
        return $this->db->delete("delete from BusinessPos where Id=" . $this->id);
    }
}
