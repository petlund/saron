<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/OrganizationFilter.php';

class OrganizationUnit extends SuperEntity{
    
    private $name;
    private $prefix;
    private $description;
    private $filter;
    private $parentTreeNode_FK;
    private $prevParentTreeNode_FK;
    private $prevParentTreeNode;
    private $orgUnitType_FK;
    private $orgRole_FK;
    private $selectionId;
    private $organizationFilter;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->prefix = (String)filter_input(INPUT_POST, "Prefix", FILTER_SANITIZE_STRING);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->orgUnitType_FK = (int)filter_input(INPUT_POST, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->prevParentTreeNode = (int)filter_input(INPUT_POST, "PrevParentTreeNode", FILTER_SANITIZE_NUMBER_INT);
        $this->prevParentTreeNode_FK = (int)filter_input(INPUT_POST, "PrevParentTreeNode_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->parentTreeNode_FK = (int)filter_input(INPUT_POST, "ParentTreeNode_FK", FILTER_SANITIZE_NUMBER_INT);

        $this->filter = (String)filter_input(INPUT_GET, "filter", FILTER_SANITIZE_STRING);

        $this->selectionId = (int)filter_input(INPUT_GET, "SelectionId", FILTER_SANITIZE_NUMBER_INT);
        $this->organizationFilter = new OrganizationFilter($db, $saronUser);

    }
    
    
    function select($id = -1){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectOptions();       
        default:
            return $this->selectDefault($id);
        }
    }

    
       
    function selectOptions(){
        $select = "SELECT -1 as Value, '  -'  as DisplayText ";
        $select.= "Union ";
        $select.= "SELECT Tree.Id as Value, Concat(Tree.Name, ' (', Typ.Name, ')')  as DisplayText ";
        $from = "FROM Org_Tree as Tree inner join Org_UnitType as Typ on Typ.Id= Tree.OrgUnitType_FK ";
        $where = "";    

        switch ($this->source){
        case SOURCE_LIST:
            break;
        case SOURCE_EDIT:
            $where = "WHERE Tree.Id <> " . $this->id . " ";    
            break;
        default:
            $where = "";    
            break;
        }
        If($this->filter === 'yes'){
            $where = "WHERE NOT (Typ.SubUnitEnabled = 0 OR Tree.Id IN (" . $this->selectSubNodesSql($this->id) . ")) ";
        }        

        $result = $this->db->select($this->saronUser, $select , $from, $where, "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    
    
    function selectDefault($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);
        $rec = RECORDS;
        //filter all nodes witch not have childs and all child below curret node
        
        $select = "Select stat.*, '" . $this->uppercaseSearchString . "' as searchString, Path, ";
        $select.= "Typ.Name as UnitTypeName, ParentUnitName, Tree.OrgUnitType_FK, Typ.PosEnabled, Tree.Name, Tree.ParentTreeNode_FK, Tree.Prefix, Tree.Description, ";
        $select.= "Typ.Id as TypeId, Tree.Id, Typ.SubUnitEnabled, Tree.UpdaterName, Tree.Updated, ";
        $select.= $this->getAppCanvasSql();
        $select.= "(Select count(*) from Org_Tree as Tree1 where Tree1.ParentTreeNode_FK = Tree.Id) as HasSubUnit, ";
        $select.= "(Select count(*) from Org_Pos as Pos1 where Tree.Id = Pos1.OrgTree_FK) as HasPos, ";
                
        $select.= $this->saronUser->getRoleSql(false);
        
        $from = "from Org_Tree as Tree ";
        $from.= "inner join " . $this->subNodesSql() . " as SubNodes on SubNodes.RootId = Tree.Id ";
        $from.= "left outer join (Select Id, Name as ParentUnitName from Org_Tree as Parent) as ParentUnit on Tree.ParentTreeNode_FK = ParentUnit.Id ";
        $from.= "inner join Org_UnitType as Typ on Tree.OrgUnitType_FK = Typ.Id ";
        $from.= "left outer join (" . $this->getStatusSQL() .  ") as stat on Tree.Id = stat.sumId ";
        
        $where = "";
        
        if($id < 0){
            switch ($this->appCanvasPath){
                case TABLE_NAME_UNITTREE:            
                    if($this->parentId < 1){
                        $where = "WHERE ParentTreeNode_FK is null And ";
                    }
                    else{
                        $where = "WHERE ParentTreeNode_FK = " . $this->parentId . " And ";                    
                    }
                    $where.= $this->organizationFilter->getTreeSearchFilterSql($this->uppercaseSearchString);
                    break;
                case TABLE_NAME_UNITTREE . "/" . TABLE_NAME_UNIT:            
                    $where = "WHERE ";
                    $where.= "ParentTreeNode_FK = " . $this->parentId . " And ";
                    $where.= $this->organizationFilter->getTreeSearchFilterSql($this->uppercaseSearchString);
                    break;
                case TABLE_NAME_UNITLIST:
                    $where.= "WHERE ";
                    //$where.= $this->organizationFilter->getPeopleFilterSql($this->groupId);
                    $where.= $this->organizationFilter->getSearchFilterSql($this->uppercaseSearchString);
                break;
                case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_UNIT:
                    $where = "WHERE OrgUnitType_FK = " . $this->parentId . " ";
                break;
                case TABLE_NAME_ROLE . "/" . TABLE_NAME_UNIT:
                    $from.= "inner join ("
                            . "select OrgTree_FK from Org_Pos where OrgRole_FK = " . $this->parentId . ""
                            . " group by OrgTree_FK) as Pos on Pos.OrgTree_FK = Tree.Id ";
                    break;
                default:
                    $where = "";
                break;
            }            
        }
        else{
            $where = "WHERE Tree.Id = " . $id . " ";
            $rec = RECORD;
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }

    
    function subNodesSql(){
        $rootRoleLongName = "' / ', (Select group_concat(DISTINCT Role.Name SEPARATOR ' / ') as Path from Org_Pos as Pos inner join Org_Role as Role on Role.Id = Pos.OrgRole_FK WHERE Pos.OrgTree_FK = RootId group by Pos.OrgTree_FK) ";
        $treeRoleLongName = "' / ', (Select group_concat(DISTINCT Role.Name SEPARATOR ' / ') as Path from Org_Pos as Pos inner join Org_Role as Role on Role.Id = Pos.OrgRole_FK WHERE Pos.OrgTree_FK = Tree.Id group by Pos.OrgTree_FK) ";
        
        $longName = "concat(IF(Prefix is not null, concat(Prefix, ' '),''), name, " . $rootRoleLongName . ") ";
        $tLongName = "concat(IF(Prefix is not null, concat(Tree.Prefix, ' '),''), Tree.name, " . $treeRoleLongName . ") ";

        $sql = "(WITH RECURSIVE Root AS (";
        $sql.= "SELECT Id, Id as RootId, " . $longName . " as LongName ";
        $sql.= "FROM Org_Tree ";
        $sql.= "UNION ALL ";
        $sql.= "SELECT Tree.Id, Root.Id as RootId, " . $tLongName.  " as LongName ";
        $sql.= "FROM Root, Org_Tree Tree ";
        $sql.= "WHERE Tree.ParentTreeNode_FK = Root.Id ";
        $sql.= ") ";
        $sql.= "SELECT RootId, group_concat(DISTINCT LongName SEPARATOR ' / ') as Path "; 
        $sql.= "FROM Root ";
        $sql.= "Group by RootId) "; 

        return $sql;
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
        $sql.= "WHERE ot.ParentTreeNode_FK = st.Id ";
        $sql.= "AND " . $this->organizationFilter->getTreeSearchFilterSql($this->uppercaseSearchString, "Name");
        $sql.= ") ";
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
        $sqlInsert = "INSERT INTO Org_Tree (Prefix, Name, Description, OrgUnitType_FK, ParentTreeNode_FK, UpdaterName, Updater) ";
        $sqlInsert.= "VALUES (";
        if(strlen($this->prefix) > 0){
            $sqlInsert.= "'" . $this->prefix  . "', ";
        }
        else{
            $sqlInsert.= "null, ";            
        }
        $sqlInsert.= "'" . $this->name  . "', ";
        $sqlInsert.= "'" . $this->description . "', ";
        $sqlInsert.= "'" . $this->orgUnitType_FK . "', ";

        if($this->parentId > 0){
            $sqlInsert.= "'" . $this->parentId . "', "; //ParentTreeNode_FK
        }
        else{
            $sqlInsert.= "null, ";                                
        }
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "', ";
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Tree", "Id");
        return $this->select($id);
    }
    
    
    function update(){
        $update = "UPDATE Org_Tree ";
        $set = "SET ";        
        if(strlen($this->prefix) > 0){
            $set.= "Prefix='" . $this->prefix . "', ";        
        }
        else{
            $set.= "Prefix=null, ";                    
        }
        $set.= "Name='" . $this->name . "', ";        
        $set.= "Description='" . $this->description . "', ";   
        if($this->orgUnitType_FK > 0){ // On edit OrgUnitType_FK === 0
            $set.= "OrgUnitType_FK='" . $this->orgUnitType_FK . "', ";   
        }
        if($this->parentTreeNode_FK >= 0){
            $set.= "ParentTreeNode_FK='" . $this->parentTreeNode_FK . "', ";        
        }
        else{
            $set.= "ParentTreeNode_FK=null, ";        
        }
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "' ";
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select();
    }

    function delete(){
        return $this->db->delete("delete from Org_Tree where Id=" . $this->id);
    }
}

