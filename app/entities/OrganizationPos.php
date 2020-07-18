<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationPos extends SuperEntity{
    
    private $posId;
    private $multiPos;
    private $posTreeId;
    private $people_FK;
    private $prevPeople_FK;
    private $orgPosStatus_FK;
    private $orgRole_FK;
    private $orgTreeNode_FK;
    private $org_Role_UnitType_FK;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->posId = (int)filter_input(INPUT_POST, "PosId", FILTER_SANITIZE_NUMBER_INT);
        $this->multiPos = (int)filter_input(INPUT_POST, "MultiPos", FILTER_SANITIZE_NUMBER_INT);
        $this->posTreeId = (int)filter_input(INPUT_POST, "PosTreeId", FILTER_SANITIZE_NUMBER_INT);
        $this->prevPeople_FK = (int)filter_input(INPUT_POST, "PrevPeople_FK", FILTER_SANITIZE_NUMBER_INT);
        
        $this->people_FK = (int)filter_input(INPUT_POST, "People_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->people_FK === 0){
            $this->people_FK = (int)filter_input(INPUT_GET, "People_FK", FILTER_SANITIZE_NUMBER_INT);
        }

        $this->orgPosStatus_FK = (int)filter_input(INPUT_POST, "OrgPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        
        $this->orgTreeNode_FK = (int)filter_input(INPUT_POST, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->orgTreeNode_FK === 0){        
            $this->orgTreeNode_FK = (int)filter_input(INPUT_GET, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        }
        $this->org_Role_UnitType_FK = (int)filter_input(INPUT_POST, "Org_Role_UnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        
    }


    function select($id = -1, $rec = RECORDS){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
        case "pos":
            return $this->selectPersonPos($id, $rec);
        default:
            return $this->selectDefault($id, $rec);
        }
    }

    
    function selectDefault($id = -1, $rec=RECORDS){
        $select = "SELECT Pos.*, Role.*, Pos.Id as PosId, PosTree.Id as PosTreeId, ";
        $select.= getPersonSql("pPrev", "PrevPerson", true);
        $select.= "Role.Name as RoleName, ";
        $select.= getMemberStateSql("pCur", "MemberState", true);
        $select.= getFieldSql("pCur", "Email", "EmailEncrypt", "", true, true);
        $select.= getFieldSql("pCur", "Mobile", "MobileEncrypt", "", true, true);
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "FROM Org_Pos as Pos inner join Org_Role Role on Pos.OrgRole_FK = Role.Id ";
        $from.= "inner join Org_Pos_Tree as PosTree on Pos.Id = PosTree.Org_Pos_FK ";
        $from.= "left outer join People as pCur on pCur.Id=Pos.People_FK ";
        $from.= "left outer join People as pPrev on pPrev.Id=Pos.PrevPeople_FK ";
        
        $where = "";

        if($id > 0){
            $where.= "WHERE Pos.Id = " . $id . " ";
        }
        else if($this->orgTreeNode_FK > 0){
            $where.= "WHERE Org_Tree_FK = " . $this->orgTreeNode_FK . " ";            
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }


    function selectPersonPos($Id = -1, $rec=RECORDS){
        $select = "SELECT *, Id as PosId, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Pos ";
        $where = "WHERE People_FK = " . $this->people_FK . " and OrgPosStatus_FK < 3 ";
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;        
    }

    function selectOptions(){
        $select = "SELECT Pos.Id as Value, Concat(Role.Name, ' ', UnitType.Name, ' ', Tree.Name) as DisplayText ";
        $from = "FROM Org_Pos as Pos inner join Org_Pos_Tree as PosTree on PosTree.Org_Pos_FK=Pos.Id ";
        $from.= "inner join Org_Tree as Tree on PosTree.Org_Tree_FK=Tree.Id ";
        $from.= "inner join Org_UnitType as UnitType on UnitType.Id = Tree.OrgUnitType_FK ";
        $from.= "inner join Org_Role as Role on Role.Id = Pos.OrgRole_FK ";
        $where = "";
        if($this->people_FK < 0){
            $where = "WHERE (People_FK is null or People_FK = 0) and  OrgPosStatus_FK = 4 ";
        }
        else {
            $where = "WHERE People_FK = " . $this->people_FK . " ";
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, "Order by DisplayText ", "", "Options");    
        return $result; 

//        $select = "SELECT id as Value, Name as DisplayText ";
//        $result = $this->db->select($this->saronUser, $select , "FROM Org_UnitType ", "", "Order by DisplayText ", "", "Options");    
//        return $result; 
    }
    
    
    function insert(){
        $sqlInsert1 = "INSERT INTO Org_Pos (People_FK, OrgPosStatus_FK, Org_Role_UnitType_FK, OrgRole_FK, Updater) ";
        $sqlInsert1.= "VALUES (";
        $sqlInsert1.= "'" . $this->people_FK . "', ";
        $sqlInsert1.= "'" . $this->orgPosStatus_FK . "', ";
        $sqlInsert1.= "'" . $this->org_Role_UnitType_FK . "', ";
        $sqlInsert1.= "'" . $this->orgRole_FK . "', ";
        $sqlInsert1.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert1, "Org_Pos", "Id");
        
        $sqlInsert2 = "INSERT INTO Org_Pos_Tree (Org_Pos_FK, Org_Tree_FK) ";
        $sqlInsert2.= "VALUES (";
        $sqlInsert2.= "'" . $id . "', ";
        $sqlInsert2.= "'" . $this->orgTreeNode_FK . "') ";
                
        $treeId = $this->db->insert($sqlInsert2, "Org_Pos_Tree", "Id");

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
        $set.= "People_FK='" . $this->people_FK . "', ";        
        $set.= "Updater='" . $this->saronUser->ID . "' ";
        $where = "WHERE id=" . $this->posId;
        $this->db->update($update, $set, $where);
        return $this->select($this->posId, RECORD);
    }

    function delete(){
        $this->db->delete("delete from Org_Pos_Tree where Org_Pos_FK =" . $this->posId . " and Org_Tree_FK = " . $this->orgTreeNode_FK);
        return  $this->db->delete("delete from Org_Pos where Id=" . $this->posId);
    }
}
