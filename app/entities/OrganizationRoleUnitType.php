    <?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationRoleUnitType extends SuperEntity{
    
    private $id;
    private $roleName;
    private $description;
    private $orgRole_FK;
    private $sortOrder;
    private $orgUnitType_FK;    
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        
        $this->sortOrder = (int)filter_input(INPUT_POST, "SortOrder", FILTER_SANITIZE_NUMBER_INT);
        $this->roleName = (String)filter_input(INPUT_POST, "RoleName", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->orgRole_FK === 0){
            $this->orgRole_FK = (int)filter_input(INPUT_GET, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        }
        
        $this->orgUnitType_FK = (int)filter_input(INPUT_POST, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->orgUnitType_FK === 0){
            $this->orgUnitType_FK = (int)filter_input(INPUT_GET, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        }        
    }


    function select($id = -1, $req = RECORDS){
        switch ($this->selection){
        case "unitTypes":
            return $this->selectUnitTypes($id, $req);       
        case "role":
            return $this->selectRole($id, $req);       
        default:
            return $this->selectDefault($id, $req);
        }
    }

    
    function selectDefault($id = -1, $rec=RECORDS){
        $select = "SELECT   *, Role.Name as RoleName, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Role as Role inner join `Org_Role-UnitType` as Rut on Rut.OrgRole_FK = Role.Id ";
        $from.= "inner join Org_UnitType as Typ on Rut.OrgUnitType_FK = Typ.Id ";
        if($id > 0){
            $where = "WHERE Rut.Id = " . $id . " ";
        }
        else{
            $where = "";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    function selectRole($id = -1, $rec=RECORDS){
        
        if($id > 0){
            $where = "WHERE Rut.Id= " . $id . " ";            
        }
        else if($this->orgUnitType_FK > 0){
            $where = "WHERE Rut.OrgUnitType_FK = " . $this->orgUnitType_FK . " ";

            $subSelect = '(Select Count(*) ';
            $subSelect.= 'From Org_Pos as Pos inner Join Org_Tree as Tree on Pos.OrgTree_FK = Tree.Id ';
            $subSelect.= 'Where Pos.OrgRole_FK = Rut.OrgRole_FK AND Tree.OrgUnitType_FK = Rut.OrgUnitType_FK ';
            $subSelect.= ') as PosOccurrency';
        }
        else{
            $where = "";
        }

        $select = "SELECT *, Role.Name as RoleName, " . $subSelect . ", ";
        $select.= $this->saronUser->getRoleSql(false) . " ";

        $from = "FROM Org_Role as Role inner join `Org_Role-UnitType` as Rut on Rut.OrgRole_FK = Role.Id ";

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    
    function selectUnitTypes($id = -1, $rec=RECORDS){
        $subSelect = '(SELECT GROUP_CONCAT("- ", TreeName SEPARATOR "<br>") '
                . 'FROM ('
                . 'Select Tree.Name as TreeName '
                . 'From Org_Tree as Tree ' 
                    . 'inner join Org_Pos as Pos on Pos.OrgTree_FK=Tree.Id '
                . 'Where Tree.OrgUnitType_FK = Rut.OrgUnitType_FK  and Pos.OrgRole_FK = ' .  $this->orgRole_FK . ' '
                . 'GROUP BY Tree.Name '
                . 'ORDER BY Tree.Name '
                . ') as TreeName'
            . ') as Occurrency ';
        
        $select = "SELECT *, " ;
        $select.=$subSelect . ", ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_UnitType as Typ inner join `Org_Role-UnitType` as Rut on Rut.OrgUnitType_FK = Typ.Id ";
        if($id > 0){
            $where = "WHERE Rut.Id= " . $id . " ";            
        }
        else if($this->orgRole_FK > 0){
            $where = "WHERE Rut.OrgRole_FK = " . $this->orgRole_FK . " ";
        }
        else{
            $where = "";
        }
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    
    
    function insert(){
        $sqlInsert = "INSERT INTO `Org_Role-UnitType` (OrgRole_FK, SortOrder, OrgUnitType_FK, UpdaterName, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->orgRole_FK . "', ";
        $sqlInsert.= "'" . $this->sortOrder . "', ";
        $sqlInsert.= "'" . $this->orgUnitType_FK . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "', ";
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "`Org_Role-UnitType`", "Id");
        return $this->select($id, RECORD);
    }


    
    function update(){
        $update = "UPDATE `Org_Role-UnitType` ";
        $set = "SET ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . ", ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "SortOrder=" . $this->sortOrder . " ";        
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    

    function delete(){
        return $this->db->delete("delete from `Org_Role-UnitType` where Id=" . $this->id);
    }
}
