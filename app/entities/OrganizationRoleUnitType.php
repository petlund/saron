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
        $select = "SELECT *, Role.Name as RoleName, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Role as Role inner join `Org_Role-UnitType` as Rut on Rut.OrgRole_FK = Role.Id ";
        if($this->orgUnitType_FK > 0){
            $where = "WHERE Rut.OrgUnitType_FK = " . $this->orgUnitType_FK . " ";
        }
        else{
            $where = "";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    
    function selectUnitTypes($id = -1, $rec=RECORDS){
        $select = "SELECT *, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_UnitType as Typ inner join `Org_Role-UnitType` as Rut on Rut.OrgUnitType_FK = Typ.Id ";
        if($this->orgRole_FK > 0){
            $where = "WHERE Rut.OrgRole_FK = " . $this->orgRole_FK . " ";
        }
        else{
            $where = "";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    
    function insert(){
        $sqlInsert = "INSERT INTO `Org_Role-UnitType` (OrgRole_FK, OrgUnitType_FK, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->orgRole_FK . "', ";
        $sqlInsert.= "'" . $this->orgUnitType_FK . "', ";
        $sqlInsert.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert, "`Org_Role-UnitType`", "Id");
        return $this->select($id, RECORD);
    }
    
     function update(){
        $update = "UPDATE `Org_Role-UnitType` ";
        $set = "SET ";        
        $set.= "SortOrder=" . $this->sortOrder . " ";        
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    

    function delete(){
        return $this->db->delete("delete from `Org_Role-UnitType` where Id=" . $this->id);
    }
}
