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
        
        $select = "SELECT * , ";
        $select.= $this->getAppCanvasSql();
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "from `Org_Role-UnitType` as RUT right outer join  
	(Select 
	UnitType.Id as UnitTypeId, UnitType.Name as UnitTypeName, Role.Id as RoleId, Role.Name as RoleName, Tree.Id as TreeId, Tree.Name as UnitName, 
	count(*) as Amount
	FROM Org_Pos as Pos 
		inner join Org_Role Role on Pos.OrgRole_FK = Role.Id 
		inner join Org_Tree as Tree on Tree.Id = Pos.OrgTree_FK 
		inner join Org_UnitType as UnitType on Tree.OrgUnitType_FK = UnitType.Id
	group by 
	   UnitType.Id, UnitType.Name, Role.Id, Tree.Id, Tree.Name, Role.Name) as Instances
        on RUT.OrgRole_FK = Instances.RoleId and RUT.OrgUnitType_FK = Instances.UnitTypeId ";

        $where = "WHERE Id = " . $this->parentId . " ";
        
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
    

//    function selectRole($id){
//        $select = "SELECT * ";
//        $select.= $this->getAppCanvasSql();
//        $select.= $this->saronUser->getRoleSql(false) . " ";
//        $from = "FROM Org_Role-UnitType";
//        $where = "WHERE OrgRole_FK = ";
//
//        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $this->resultType);    
//        return $result;        
//    }
//    
//

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
