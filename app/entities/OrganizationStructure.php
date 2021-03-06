<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationStructure extends SuperEntity{
    
    private $treeId;
    private $name;
    private $prefix;
    private $description;
    private $filter;
    private $parentTreeNode_FK;
    private $newParentTreeNode_FK;
    private $orgUnitType_FK;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->treeId = (int)filter_input(INPUT_POST, "TreeId", FILTER_SANITIZE_NUMBER_INT);
        if($this->treeId === 0){
            $this->treeId = (int)filter_input(INPUT_GET, "TreeId", FILTER_SANITIZE_NUMBER_INT);            
        }
        $this->prefix = (String)filter_input(INPUT_POST, "Prefix", FILTER_SANITIZE_STRING);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->filter = (String)filter_input(INPUT_GET, "filter", FILTER_SANITIZE_STRING);
        $this->orgUnitType_FK = (int)filter_input(INPUT_POST, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->newParentTreeNode_FK = (int)filter_input(INPUT_POST, "ParentTreeNode_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->parentTreeNode_FK = (int)filter_input(INPUT_GET, "ParentTreeNode_FK", FILTER_SANITIZE_NUMBER_INT);
    }
    
    function select($id = -1, $rec = RECORDS){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
        case "single_node":
            return $this->selectDefault($this->treeId, RECORD);       
        default:
            return $this->selectDefault($id, $rec);
        }
    }

    function selectOptions_OLD(){
        $select = "SELECT Tree.Id as Value, Concat(Role.Name, ' ', Typ.Name, ' ', Tree.Name) as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM Org_Tree as Tree inner join Org_UnitType as Typ on Typ.Id= Tree.OrgUnitType_FK inner join Org_Role as Role on Role.Id = Tree.OrgRole_FK ", "", "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function selectOptions(){
            
        $select = "SELECT -1 as Value, '  -'  as DisplayText ";
        $select.= "Union ";
        $select.= "SELECT Tree.Id as Value, Concat(Tree.Name, ' (', Typ.Name, ')')  as DisplayText ";
        $from = "FROM Org_Tree as Tree inner join Org_UnitType as Typ on Typ.Id= Tree.OrgUnitType_FK ";
        $where = "";    
        
            If($this->filter === 'yes'){
            $where = "WHERE NOT (Typ.SubUnitEnabled = 0 OR Tree.Id IN (" . $this->selectSubNodesSql($this->treeId) . ")) ";
        }        
        $result = $this->db->select($this->saronUser, $select , $from, $where, "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function selectDefault($id = -1, $rec=RECORDS){
        //filter all nodes witch not have childs and all child below curret node
        
        $select = "Select stat.*, OrgUnitType_FK, Typ.PosEnabled, Tree.Name, Tree.ParentTreeNode_FK, Tree.Prefix, Tree.Description, Typ.Id as TypeId, Tree.Id as TreeId, Typ.SubUnitEnabled, Tree.UpdaterName, Tree.Updated, ";
        $select.= "(Select count(*) from Org_Tree as Tree1 where Tree1.ParentTreeNode_FK = Tree.Id) as HasSubUnit, ";
        $select.= "(Select count(*) from Org_Pos as Pos1 where Tree.Id = Pos1.OrgTree_FK) as HasPos, ";
        IF($this->newParentTreeNode_FK !== $this->parentTreeNode_FK AND $rec===RECORD){
            $select.= "'1' as parentNodeChange, ";            
        }
        else{
            $select.= "'0' as parentNodeChange, ";                        
        }
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "from Org_Tree as Tree ";
        $from.= "inner join Org_UnitType as Typ on Tree.OrgUnitType_FK = Typ.Id ";
        $from.= "left outer join (" . $this->getStatusSQL() .  ") as stat on Tree.Id = stat.sumId ";
        $where = "";    
        if($id < 0){
            if($this->parentTreeNode_FK === -1){
                $where = "WHERE ParentTreeNode_FK is null ";
            }
            else{
                $where = "WHERE ParentTreeNode_FK = " . $this->parentTreeNode_FK . " ";                
            }
            $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), RECORDS);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , $from, "WHERE Tree.Id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }

    
    function selectSubNodesSql($nodeId){
        $sql = "(With RECURSIVE SubTree as ( ";
        $sql.= "Select Id, Name, 1 as orglevel, ParentTreeNode_FK as parent from Org_Tree ";
        $sql.= "Union All ";
        $sql.= "Select ot.Id as Id, ot.name, st.orglevel + 1 as orglevel, st.parent as parent ";
        $sql.= "From Org_Tree as ot inner join SubTree as st ";
        $sql.= "where st.Id = ot.ParentTreeNode_FK) ";

        $sql.= "Select Id from SubTree  where parent = " . $nodeId . " or Id = " . $nodeId . ")";        
                
        return $sql;
    }
    
    function getStatusSQL(){
        $sql = "WITH RECURSIVE Sub_Tree AS (";
        $sql.= "SELECT Id, Id as sumId, 'top' as sourceTable ";
        $sql.= "FROM Org_Tree "; 
        $sql.= "UNION ALL ";
        $sql.= "SELECT ot.Id as Id, sumId, 'sub' as sourceTable ";
        $sql.= "FROM Org_Tree ot inner join Sub_Tree st ";
        $sql.= "WHERE ot.ParentTreeNode_FK = st.Id) ";
        $sql.= "select "; 
        $sql.= "sumId, ";
        $sql.= "sum(case when OrgPosStatus_FK = 2 and sourceTable = 'top' then 1  else 0 end) as statusProposal, ";
        $sql.= "sum(case when OrgPosStatus_FK = 4 and sourceTable = 'top' then 1  else 0 end) as statusVacant, ";
        $sql.= "sum(case when OrgPosStatus_FK = 1 and sourceTable = 'top' then 1  else 0 end) as statusCommitted, ";
        $sql.= "sum(case when OrgPosStatus_FK = 5 and sourceTable = 'top' then 1  else 0 end) as statusNotAdded, ";
        $sql.= "sum(case when OrgPosStatus_FK = 2 and sourceTable = 'sub' then 1  else 0 end) as statusSubProposal, ";
        $sql.= "sum(case when OrgPosStatus_FK = 4 and sourceTable = 'sub'  then 1  else 0 end) as statusSubVacant, ";
        $sql.= "sum(case when OrgPosStatus_FK = 1 and sourceTable = 'sub'  then 1  else 0 end) as statusSubCommitted, ";
        $sql.= "sum(case when OrgPosStatus_FK = 5 and sourceTable = 'sub'  then 1  else 0 end) as statusSubNotAdded ";
        $sql.= "FROM Sub_Tree left outer join Org_Pos on Sub_Tree.Id = Org_Pos.OrgTree_FK ";
        $sql.= "Group by sumId ";
        
        return $sql;
    }
    
    function insert(){
        $sqlInsert = "INSERT INTO Org_Tree (Prefix, Name, Description, OrgUnitType_FK, ParentTreeNode_FK, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->prefix  . "', ";
        $sqlInsert.= "'" . $this->name  . "', ";
        $sqlInsert.= "'" . $this->description . "', ";
        $sqlInsert.= "'" . $this->orgUnitType_FK . "', ";

        if($this->parentTreeNode_FK > 0){
            $sqlInsert.= "'" . $this->parentTreeNode_FK . "', ";
        }
        else{
            $sqlInsert.= "null, ";                                
        }
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Tree", "Id");
            return $this->select($id, RECORD);
    }
    
    
    function update(){
        $update = "UPDATE Org_Tree ";
        $set = "SET ";        
        $set.= "Prefix='" . $this->prefix . "', ";        
        $set.= "Name='" . $this->name . "', ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "OrgUnitType_FK='" . $this->orgUnitType_FK . "', ";   
        if($this->newParentTreeNode_FK >= 0){
            $set.= "ParentTreeNode_FK='" . $this->newParentTreeNode_FK . "', ";        
        }
        else{
            $set.= "ParentTreeNode_FK=null, ";        
        }
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "' ";
        $where = "WHERE id=" . $this->treeId;
        $this->db->update($update, $set, $where);
        return $this->select($this->treeId, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from Org_Tree where Id=" . $this->treeId);
    }
}
