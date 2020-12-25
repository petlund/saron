<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationRole extends SuperEntity{
    
    private $id;
    private $name;
    private $description;
    private $unitTypeId;
    private $orgTreeNode_FK;
    private $roleType;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        if($this->id  === 0){
            $this->id = (int)filter_input(INPUT_GET, "Id", FILTER_SANITIZE_NUMBER_INT);
        }
        
        $this->roleType = (int)filter_input(INPUT_POST, "RoleType", FILTER_SANITIZE_NUMBER_INT);

        $this->orgTreeNode_FK = (int)filter_input(INPUT_POST, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->orgTreeNode_FK  === 0){
            $this->orgTreeNode_FK = (int)filter_input(INPUT_GET, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        }
        
        $this->orgUnitType_FK = (int)filter_input(INPUT_POST, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->orgUnitType_FK === 0){        
            $this->orgUnitType_FK = (int)filter_input(INPUT_GET, "OrgUnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        }
        
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        
    }


    function select($id = -1, $req = RECORDS){
        if($id < 0 && $this->id > 0){
            $id = $this->id;
        }
        switch ($this->selection){
        case "options":
            return $this->selectOptions($id);       
        default:
            return $this->selectDefault($id, $req);
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

 
    function selectDefault($id = -1, $rec=RECORDS){
        $select = "SELECT *, Role.UpdaterName as UpdaterName, Role.Updated as Updated, ";
        $select.= "(Select count(*) from `Org_Role-UnitType` as UnitRole WHERE UnitRole.OrgRole_FK = Role.Id) as HasChild, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Role as Role ";
        if($this->unitTypeId > 0){
            $from = "FROM Org_Role as Role inner join `Org_Role-UnitType` as Rut on Rut.OrgRole_FK = Role.Id ";
            $where = "WHERE OrgUnitType_FK = " . $this->orgUnitType_FK . " ";
        }
        else{
            $where = "";
        }
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM Org_Role as Role ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }

    function selectOptions($id){
        $select = "SELECT Role.Id  as Value, Concat(Role.Name, if(RoleType = 1, ' (Org)','')) as DisplayText ";
        $from = "FROM Org_Role as Role "; 
        $where = "";

        if($this->orgUnitType_FK > 0){
            $from.= "inner join `Org_Role-UnitType` Typ on Role.Id = Typ.OrgRole_FK "; 
            $where = "WHERE OrgUnitType_FK = " . $this->orgUnitType_FK . " ";
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
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from Org_Role where Id=" . $this->id);
    }
}



