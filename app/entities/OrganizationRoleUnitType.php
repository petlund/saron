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


    function select(){
        switch ($this->tablePath){
        case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE:
            return $this->selectUnitTypes();       
        case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE:
            return $this->selectRole();       
        default:
            return $this->selectDefault();
        }
    }

    function selectDefault($idFromCreate = -1){
        $select = "SELECT *, Role.Name as RoleName, ";
        $select.= $this->getTablePathSql();
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Role as Role inner join `Org_Role-UnitType` as Rut on Rut.OrgRole_FK = Role.Id ";
        $from.= "inner join Org_UnitType as Typ on Rut.OrgUnitType_FK = Typ.Id ";
        if($idFromCreate > 0){
            $where = "WHERE Rut.Id = " . $idFromCreate . " ";
        }
        else{
            $where = "";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $this->resultType);    
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
