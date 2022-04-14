
<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationRole extends SuperEntity{
    
    private $name;
    private $description;
    private $orgTreeNode_FK;
    private $roleType;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->roleType = (int)filter_input(INPUT_POST, "RoleType", FILTER_SANITIZE_NUMBER_INT);
        $this->orgTreeNode_FK = (int)filter_input(INPUT_POST, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgUnitType_FK = (int)filter_input(INPUT_POST, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        
    }


    function select(){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectOptions();       
        case RECORDS:
            return $this->selectDefault();       
        case RECORD:
            return $this->selectDefault();       
        default:
            return $this->selectDefault();
        }
    }


    
    function checkRoletData(){
        $error = array();

        if(strlen(trim($this->name)) === 0){
            $error["Result"] = "ERROR";
            $error["Message"] = "Det saknas ett namn på rollen";
            throw new Exception(json_encode($error));
        }
        if($this->db->fieldValueExist($this->name, $this->id, "Name", "Org_Role")){
            $error["Result"] = "ERROR";
            $error["Message"] = "Det finns redan en organisationsroll med namnet: '" . $this->name . "'";
            throw new Exception(json_encode($error));
        }
    }

    
    
    // from entity: role-unittype
    function selectRole($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);
        $rec = RECORDS;

        $where = "";         
        if($id < 0){
            switch ($this->tablePath){
                case TABLE_NAME_ROLE:            
                    $where = "";
                    break;
                case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE:            
                    $where.= "WHERE Rut.OrgUnitType_FK = " . $this->parentid . " ";
                    break;
                default:
                    $where = "";
            }
        }
        else{
            $where.= "WHERE Role.Id = " . $id . " ";
            $rec = RECORD;
        }

        if($id > 0){
            $where = "WHERE Rut.Id= " . $id . " ";            
        }
        else if($this->parentId > 0){
            $where = "WHERE Rut.OrgRole_FK = " . $this->parentId . " ";
        }

        $subSelect = '(Select Count(*) ';
        $subSelect.= 'From Org_Pos as Pos inner Join Org_Tree as Tree on Pos.OrgTree_FK = Tree.Id ';
        $subSelect.= 'Where Pos.OrgRole_FK = Rut.OrgRole_FK AND Tree.OrgUnitType_FK = Rut.OrgUnitType_FK ';
        $subSelect.= ') as PosOccurrency';

        $select = "SELECT *, Role.Name as RoleName, " . $subSelect . ", ";
        $select.= $this->getTablePathSql();
        $select.= $this->saronUser->getRoleSql(false) . " ";

        $from = "FROM Org_Role as Role inner join `Org_Role-UnitType` as Rut on Rut.OrgRole_FK = Role.Id ";

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $this->resultType);    
        return $result;
    }

    
    
    function selectDefault($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);
        $rec = RECORDS;
        
        $select = "SELECT *, Role.UpdaterName as UpdaterName, Role.Updated as Updated, ";
        $select.= $this->getTablePathSql();
        $select.= "(Select count(*) from `Org_Role-UnitType` as UnitRole WHERE UnitRole.OrgRole_FK = Role.Id) as HasChild, ";
        $select.= "(Select count(*) from Org_Pos as Pos WHERE Pos.OrgRole_FK=Role.Id) as PosOccurrency, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Role as Role ";
        $where = "";
        
        if($id < 0){
            switch ($this->tablePath){
                case TABLE_NAME_ROLE:            
                    break;
                case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE:            
                    $from.= "inner join `Org_Role-UnitType` as Rut on Rut.OrgRole_FK = Role.Id ";
                    $where= "WHERE Rut.OrgUnitType_FK = " . $this->parentId . " ";
                    break;
                default:
            }
        }
        else{
            $rec = RECORD;
            $where = "WHERE Role.Id = " . $id . " ";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }

    
    
    function selectOptions(){
        $select = "SELECT Role.Id  as Value, Concat(Role.Name, if(RoleType = 1, ' (Org)','')) as DisplayText ";
        $from = "FROM Org_Role as Role "; 
        $where = "";

        if($this->source === SOURCE_CREATE){
//            $from.= "inner join `Org_Role-UnitType` as RUT on Role.Id = RUT.OrgRole_FK  "; 
//            $from.= "inner join Org_UnitType as UType on UType.Id = RUT.OrgUnitType_FK "; 
//            $from.= "inner join Org_Tree as Tree on Tree.OrgUnitType_FK = UType.Id "; 
//            $where = "WHERE UType.Id = " . $this->parentId . " ";
            
            $where = "WHERE Role.Id not in (Select OrgRole_FK from `Org_Role-UnitType` WHERE OrgUnitType_FK = " . $this->parentId . ") ";
        }
            
        $result = $this->db->select($this->saronUser, $select , $from, $where, "Order by DisplayText ", "", "Options");    
        return $result; 
    }
    
    
    function insert(){
        $this->checkRoletData();
        $sqlInsert = "INSERT INTO Org_Role (Name, RoleType, Description, UpdaterName, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->name . "', ";
        $sqlInsert.= "'" . $this->roleType . "', ";
        $sqlInsert.= "'" . $this->description . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "', ";
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Role", "Id");
        return $this->select($id, RECORD);
    }
    
    
    function update(){
        $this->checkRoletData();
        $update = "UPDATE Org_Role ";
        $set = "SET ";        
        $set.= "Name='" . $this->name . "', ";        
        $set.= "RoleType='" . $this->roleType . "', ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "' ";
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from Org_Role where Id=" . $this->id);
    }
}



