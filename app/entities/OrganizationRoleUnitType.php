    <?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationRoleUnitType extends SuperEntity{
    
    private $roleName;
    private $description;
    private $orgRole_FK;
    private $sortOrder;
    private $orgUnitType_FK;   
    private $businessKeyName;

    
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
        switch ($this->appCanvasPath){
            case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE . "/" . TABLE_NAME_POS_INSTANCES:
                return $this->selectPosInstances();
            case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE . "/" . TABLE_NAME_POS_INSTANCES:
                return $this->selectPosInstances();
        default:
            return $this->selectDefault($id);
        }
    }
  
    
    function selectPosInstances(){
        $resultType = RECORDS;
        
        $select = "SELECT ";
        $select.= $this->getAppCanvasSql();
        $select.= $this->saronUser->getRoleSql(false) . ", ";
        $select.= "UnitType.Id as UnitTypeId, UnitType.Name as UnitTypeName, Role.Id as RoleId, Role.Name as RoleName, Tree.Id as TreeId, Tree.Name as UnitName, ";
        $select.= "'unittype/role-unittype/pos_instances' AS AppCanvasPath, 'pos_instances' AS AppCanvasName, 'edit' as user_role, ";
        $select.= "(Select count(*) from Org_Pos as P inner join Org_Tree as T on P.OrgTree_FK=T.Id where P.OrgRole_FK= Role.Id and T.Id = Tree.Id ) as Amount ";
        
        $from = "from`Org_Role-UnitType` as RUT ";
        $from.= "inner join Org_UnitType as UnitType on RUT.OrgUnitType_FK = UnitType.Id ";
        $from.= "inner join Org_Role as Role on RUT.OrgRole_FK = Role.Id ";
        $from.= "inner join Org_Tree as Tree on Tree.OrgUnitType_FK = UnitType.Id ";
        
        $where = "WHERE RUT.Id = " . $this->parentId . " ";
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $resultType);    
        return $result;        
    }
    

    function selectDefault($id){
        $resultType = RECORDS;
        $subSelect = "(Select 
	count(*) as Amount
	FROM Org_Pos as Pos 
		inner join Org_Role Role on Pos.OrgRole_FK = Role.Id 
		inner join Org_Tree as Tree on Tree.Id = Pos.OrgTree_FK 
		inner join Org_UnitType as UnitType on Tree.OrgUnitType_FK = UnitType.Id
                WHERE RUT.OrgUnitType_FK = Tree.OrgUnitType_FK AND RUT.OrgRole_FK = Pos.OrgRole_FK 
            ) as Amount, ";
        
        $select = "SELECT *, ";
        $select.= $subSelect;
        $select.= $this->getAppCanvasSql();
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM `Org_Role-UnitType` as RUT ";
        $where = "";
        
        if($id < 0){
            switch ($this->appCanvasPath){
            case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $where = "WHERE RUT.OrgUnitType_FK = " . $this->parentId . " ";
                break;
            case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $where = "WHERE RUT.OrgRole_FK = " . $this->parentId . " ";
            break;
            }
        }
        else{
            $resultType = RECORD;
            $where = "WHERE RUT.Id = " . $id . " ";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $resultType);    
        return $result;        
    }
    

    function checkData(){
        $error = array();

        switch ($this->appCanvasPath){
            case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                if($this->parentId < 1){
                    $error["Result"] = "ERROR";
                    $error["Message"] = "Du måste ange en organisatorisk enhet eller avbryta.";
                    throw new Exception(json_encode($error));
                }
            case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                if($this->parentId < 1){
                    $error["Result"] = "ERROR";
                    $error["Message"] = "Du måste ange en roll eller avbryta.";
                    throw new Exception(json_encode($error));
                }
        }
    }


    function insert(){
        $this->checkData();
       
        switch ($this->appCanvasPath){
            case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $this->OrgUnitType_FK =  $this->parentId;
                $this->OrgRole_FK = $this->orgRole_FK;
                $this->businessKeyName = "Namn på enhethetstyp";
                $this->key = $this->OrgRole_FK;
                break;
            case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $this->OrgRole_FK = $this->parentId;
                $this->OrgUnitType_FK =  $this->orgUnitType_FK;
                $this->businessKeyName = "Rollnamn";
                $this->key = $this->OrgUnitType_FK;
                break;
        }
        
        $sqlInsert = "INSERT INTO `Org_Role-UnitType` (OrgRole_FK, SortOrder, OrgUnitType_FK, UpdaterName, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->OrgRole_FK . "', ";
        $sqlInsert.= "'" . $this->sortOrder . "', ";
        $sqlInsert.= "'" . $this->OrgUnitType_FK . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "', ";
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "view_org_role_unittype", "Id", 'Koppling mellan Enhetstyp och Roll', "Kopplingsid", null, $this->saronUser);
        return $this->select($id);
    }


    
    function update(){
        $update = "UPDATE `Org_Role-UnitType` ";
        $set = "SET ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . ", ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "SortOrder=" . $this->sortOrder . " ";        
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where, $this->id, 'Koppling mellan Enhetstyp och Roll', "Kopplingsid", null, $this->saronUser);
        return $this->select($this->id);
    }

    

    function delete(){
        switch ($this->appCanvasPath){
            case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $this->businessKeyName = "Namn på enhethetstyp";
                break;
            case TABLE_NAME_ROLE . "/" . TABLE_NAME_ROLE_UNITTYPE:
                $this->businessKeyName = "Rollnamn";
                break;
        }
        
        $sql="delete from `Org_Role-UnitType` where Id=" . $this->id;
        return $this->db->delete($sql,'view_org_role_unittype', 'Id', $this->id, 'Koppling mellan Enhetstyp och Roll', "Kopplingsid", null, $this->saronUser);
    }
}
