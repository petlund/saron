<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationStructure extends SuperEntity{
    
    private $treeId;
    private $sortOrder;
    private $name;
    private $description;
    private $parentTreeNode_FK;
    private $orgUnitType_FK;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->sortOrder = (int)filter_input(INPUT_POST, "SortOrder", FILTER_SANITIZE_NUMBER_INT);
        $this->treeId = (int)filter_input(INPUT_POST, "TreeId", FILTER_SANITIZE_NUMBER_INT);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->orgUnitType_FK = (int)filter_input(INPUT_POST, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->parentTreeNode_FK = (int)filter_input(INPUT_POST, "ParentTreeNode_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->parentTreeNode_FK === 0){
            $this->parentTreeNode_FK = (int)filter_input(INPUT_GET, "ParentTreeNode_FK", FILTER_SANITIZE_NUMBER_INT);
        }
    }
    
    function select($id = -1, $rec = RECORDS){
        switch ($this->selection){
        case "options":
            return $this->selectOptions();       
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
        $select = "SELECT Tree.Id as Value, Concat(Tree.Name, ' (', Typ.Name, ')')  as DisplayText ";
        $from = "FROM Org_Tree as Tree inner join Org_UnitType as Typ on Typ.Id= Tree.OrgUnitType_FK ";
        $result = $this->db->select($this->saronUser, $select , $from, "", "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function selectDefault($treeId = -1, $rec=RECORDS){
        $select = "Select Tree.SortOrder, OrgUnitType_FK, Typ.PosEnabled, Tree.Name, Tree.Description, Typ.Id as TypeId, Tree.Id as TreeId, Typ.SubUnitEnabled, Tree.Updater, Tree.Updated, ";
        $select.= "(Select count(*) from Org_Tree as Tree1 where Tree1.ParentTreeNode_FK = Tree.Id) as HasSubUnit, ";
        $select.= "(Select count(*) from Org_Pos as Pos1 where Tree.Id = Pos1.OrgTree_FK) as HasPos, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "from Org_Tree as Tree inner join Org_UnitType as Typ on Tree.OrgUnitType_FK = Typ.Id ";
        
        if($treeId < 0){
            if($this->parentTreeNode_FK === -1){
                $where = "WHERE ParentTreeNode_FK is null ";
            }
            else{
                $where = "WHERE ParentTreeNode_FK = " . $this->parentTreeNode_FK . " ";                
            }
            $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , $from, "WHERE Tree.Id = " . $treeId . " ", $this->getSortSql(), $this->getPageSizeSql(), $rec);        
            return $result;
        }
    }

    function insert(){
        $sqlInsert = "INSERT INTO Org_Tree (SortOrder, Name, Description, OrgUnitType_FK, ParentTreeNode_FK, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->sortOrder . "', ";
        $sqlInsert.= "'" . $this->name  . "', ";
        $sqlInsert.= "'" . $this->description . "', ";
//        $sqlInsert.= "'" . $this->orgUnitType_FK . "', ";
        $sqlInsert.= "6, ";
        if($this->parentTreeNode_FK > 0){
            $sqlInsert.= "'" . $this->parentTreeNode_FK . "', ";
        }
        else{
            $sqlInsert.= "null, ";                                
        }
        $sqlInsert.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Tree", "Id");
            return $this->select($id, RECORD);
    }
    
    
    function update(){
        $update = "UPDATE Org_Tree ";
        $set = "SET ";        
        $set.= "SortOrder=" . $this->sortOrder . ", ";        
        $set.= "Name='" . $this->name . "', ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "OrgUnitType_FK='" . $this->orgUnitType_FK . "', ";   
        if($this->parentTreeNode_FK >= 0){
            $set.= "ParentTreeNode_FK='" . $this->parentTreeNode_FK . "', ";        
        }
        else{
            $set.= "ParentTreeNode_FK=null, ";        
        }
        $set.= "Updater='" . $this->saronUser->ID . "' ";
        $where = "WHERE id=" . $this->treeId;
        $this->db->update($update, $set, $where);
        return $this->select($this->treeId, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from Org_Tree where Id=" . $this->treeId);
    }
}
