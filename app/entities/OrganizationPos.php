<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationPos extends SuperEntity{
    
    private $posId;
    private $posTreeId;
    private $personId;
    private $prevPeople_FK;
    private $orgPosStatus_FK;
    private $orgRole_FK;
    private $orgTree_FK;
    private $orgSuperPos_FK;

    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->posId = (int)filter_input(INPUT_POST, "PosId", FILTER_SANITIZE_NUMBER_INT);
        $this->orgSuperPos_FK = (int)filter_input(INPUT_POST, "OrgSuperPos_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->posTreeId = (int)filter_input(INPUT_POST, "PosTreeId", FILTER_SANITIZE_NUMBER_INT);
        $this->prevPeople_FK = (int)filter_input(INPUT_POST, "PrevPeople_FK", FILTER_SANITIZE_NUMBER_INT);
        
        $this->personId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        if($this->personId === 0){
            $this->personId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        }

        $this->orgPosStatus_FK = (int)filter_input(INPUT_POST, "OrgPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        
        $this->orgTree_FK = (int)filter_input(INPUT_POST, "OrgTree_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->orgTree_FK === 0){        
            $this->orgTree_FK = (int)filter_input(INPUT_GET, "OrgTree_FK", FILTER_SANITIZE_NUMBER_INT);
        }
    }


    function select($id = -1, $rec = RECORDS){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
        case "engagement":
            return $this->selectPersonEngagement($id, $rec);
        default:
            return $this->selectDefault($id, $rec);
        }
    }

    
    function selectDefault($id = -1, $rec=RECORDS){
        $select = "SELECT Pos.*, Role.*, Pos.Id as PosId, ";
        $select.= getPersonSql("pPrev", "PrevPerson", true);
        $select.= "Role.Name as RoleName, ";
        $select.= getMemberStateSql("pCur", "MemberState", true);
        $select.= getFieldSql("pCur", "Email", "EmailEncrypt", "", true, true);
        $select.= getFieldSql("pCur", "Mobile", "MobileEncrypt", "", true, true);
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "FROM Org_Pos as Pos inner join Org_Role Role on Pos.OrgRole_FK = Role.Id ";
        $from.= "left outer join People as pCur on pCur.Id=Pos.People_FK ";
        $from.= "left outer join People as pPrev on pPrev.Id=Pos.PrevPeople_FK ";
        
        $where = "";

        if($id > 0){
            $where.= "WHERE Pos.Id = " . $id . " ";
        }
        else if($this->orgTree_FK > 0){
            $where.= "WHERE OrgTree_FK = " . $this->orgTree_FK . " ";            
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }


    
    function selectPersonEngagement($Id = -1, $rec=RECORDS){
        $select = "SELECT *, Pos.Id as PosId, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Pos as Pos ";
        $from.= "inner join " . ORG_POS_XREF . " on xref.Id =Pos.Id ";
        $where = "WHERE xref.People_FK2 = " . $this->personId . " and OrgPosStatus_FK < 3 ";
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;        
    }

    
    
    function selectOptions(){
        $select = "SELECT Pos.Id as Value, Concat(Role.Name, ' ', UnitType.Name, ' ', Tree.Name, ". EMBEDDED_SELECT_SUPERPOS . ") as DisplayText ";
        $from = "FROM Org_Pos as Pos inner join Org_Tree as Tree on Pos.OrgTree_FK=Tree.Id ";
        $from.= "inner join Org_UnitType as UnitType on UnitType.Id = Tree.OrgUnitType_FK ";
        $from.= "inner join Org_Role as Role on Role.Id = Pos.OrgRole_FK ";
        $where = "WHERE People_FK = " . $this->personId . " ";
        
        $result = $this->db->select($this->saronUser, $select , $from, "", "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function insert(){
        $sqlInsert1 = "INSERT INTO Org_Pos (People_FK, OrgPosStatus_FK, OrgSuperPos_FK, OrgRole_FK, OrgTree_FK, Updater) ";
        $sqlInsert1.= "VALUES (";
        $sqlInsert1.= "'" . $this->personId . "', ";
        $sqlInsert1.= "'" . $this->orgPosStatus_FK . "', ";
        $sqlInsert1.= "null,"; //"'" . $this->orgSuperPos_FK . "', ";
        $sqlInsert1.= "'" . $this->orgRole_FK . "', ";
        $sqlInsert1.= "'" . $this->orgTree_FK . "', ";
        $sqlInsert1.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert1, "Org_Pos", "Id");
        
        $result = $this->select($id, RECORD);
        return $result;
    }
    
    
    function update(){
        $update = "UPDATE Org_Pos ";
        $set = "SET ";   
        if($this->orgRole_FK > 0){
            $set.= "OrgRole_FK='" . $this->orgRole_FK . "', ";      
        }
        $set.= "OrgPosStatus_FK='" . $this->orgPosStatus_FK . "', ";
            
        $set.= "People_FK='" . $this->personId . "', ";        
        $set.= "Updater='" . $this->saronUser->ID . "' ";
        $where = "WHERE id=" . $this->posId;
        $response = $this->db->update($update, $set, $where);
        
        return $this->select($this->posId, RECORD);
    }

    function delete(){
        return  $this->db->delete("delete from Org_Pos where Id=" . $this->posId);
    }
}
