    <?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationRoleUnitType extends SuperEntity{
    
    private $roleName;
    private $description;
    private $orgRole_FK;
    private $sortOrder;
    private $orgUnitType_FK;    
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
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


    function select($id = -1){
        switch ($this->tablePath){
        default:
            return $this->selectDefault($id);
        }
    }

    
    
    function selectDefault($id){
        $resultType = RECORDS;
        
        $select = "SELECT * , ";
        $select.= $this->getTablePathSql();
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM `Org_Role-UnitType`";

        if($id < 0){
            switch ($this->tablePath){
            case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $where = "WHERE OrgUnitType_FK = " . $this->parentId . " ";
                break;
            case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $where = "WHERE OrgRole_FK = " . $this->parentId . " ";
                break;
            }
        }
        else{
            $resultType = RECORD;
            $where = "WHERE Id = " . $id . " ";
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $resultType);    
        return $result;        
    }
    

    function selectRole($id){
        $select = "SELECT * ";
        $select.= $this->getTablePathSql();
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Role-UnitType";
        $where = "WHERE OrgRole_FK = ";

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $this->resultType);    
        return $result;        
    }
    


    function insert(){
        switch ($this->tablePath){
            case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $OrgUnitType_FK =  $this->parentId;
                $OrgRole_FK = $this->orgRole_FK;
                break;
            case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $OrgRole_FK = $this->parentId;
                $OrgUnitType_FK =  $this->orgUnitType_FK;
                break;
        }

        $sqlInsert = "INSERT INTO `Org_Role-UnitType` (OrgRole_FK, SortOrder, OrgUnitType_FK, UpdaterName, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $OrgRole_FK . "', ";
        $sqlInsert.= "'" . $this->sortOrder . "', ";
        $sqlInsert.= "'" . $OrgUnitType_FK . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "', ";
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "`Org_Role-UnitType`", "Id");
        return $this->select($id);
    }


    
    function update(){
        $update = "UPDATE `Org_Role-UnitType` ";
        $set = "SET ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . ", ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "SortOrder=" . $this->sortOrder . " ";        
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }

    

    function delete(){
        return $this->db->delete("delete from `Org_Role-UnitType` where Id=" . $this->id);
    }
}
