<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationUnitType extends SuperEntity{
    
    private $id;
    private $name;
    private $description;
    private $subUnitEnabled;
    private $posEnabled;
    private $orgRole_FK;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->subUnitEnabled = (int)filter_input(INPUT_POST, "SubUnitEnabled", FILTER_SANITIZE_NUMBER_INT);
        $this->posEnabled = (int)filter_input(INPUT_POST, "PosEnabled", FILTER_SANITIZE_NUMBER_INT);

        $this->orgRole_FK = (int)filter_input(INPUT_GET, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
    }

    function checkUnitTypeData(){
        $error = array();

        if(strlen(trim($this->name)) === 0){
            $error["Result"] = "ERROR";
            $error["Message"] = "Det saknas ett namn på den organisatoriska enheten";
            throw new Exception(json_encode($error));
        }
        if($this->db->fieldValueExist($this->name, $this->id, "Name", "Org_UnitType")){
            $error["Result"] = "ERROR";
            $error["Message"] = "Det finns redan en organisatorisk enhet med namnet: '" . $this->name . "'";
            throw new Exception(json_encode($error));
        }
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


    
    function selectDefault($idFromCreate = -1 ){
        $id = $this->getId($idFromCreate, $this->id);
        $rec = RECORDS;

        $select = "SELECT Typ.Id, Typ.PosEnabled, Typ.SubUnitEnabled, Typ.Name, Typ.Description, Typ.UpdaterName, Typ.Updated, "; 
        $select.= $this->getTablePathSql();
        $select.= "(select If(count(*)=0,0,1) From Org_Tree as Tree where  Tree.OrgUnitType_FK = Typ.Id) as UsedInUnit, ";
        $select.= "(select sum((select count(*) from Org_Tree as T2 where T2.ParentTreeNode_FK=T1.Id)) From Org_Tree as T1 where  T1.OrgUnitType_FK = Typ.Id Group by T1.OrgUnitType_FK) as UseChild, ";
        $select.= "(select sum((select count(*) from Org_Pos as P where P.OrgTree_FK = T1.Id)) From Org_Tree as T1 where  T1.OrgUnitType_FK = Typ.Id Group by T1.OrgUnitType_FK) as UseRole, ";
        $select.= "(Select count(*) from `Org_Role-UnitType` as UnitRole WHERE UnitRole.OrgUnitType_FK = Typ.Id) as HasPos, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_UnitType as Typ ";
 
        if($id < 0){
            switch ($this->tablePath){
                case TABLE_NAME_UNITTYPE:            
                    $where = "";
                    break;
                case TABLE_NAME_ROLE . "/" . TABLE_NAME_UNITTYPE:            
                    $from.= "inner join `Org_Role-UnitType` as Rut on Rut.OrgUnitType_FK = Typ.Id ";
                    $where = "WHERE Rut.OrgRole_FK = " . $this->parentId . " ";
                    break;
                default:
                    $where = "";
            }                    
        }
        else{
            $rec = RECORDS;
            $where = "WHERE Typ.Id= " . $id . " ";            
        }        
        
//        if($this->posEnabled > 0){
//            $from = "FROM Org_UnitType as Typ inner join `Org_Role-UnitType` as Rut on Rut.OrgUnitType_FK = Typ.Id ";
//            $where = "WHERE OrgRole_FK = " . $this->posEnabled . " ";
//        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    // from entity: role-unittyes
    function selectUnitTypes($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);        
        $rec = RECORDS;
        
        $subSelect = '(SELECT GROUP_CONCAT("- ", TreeName SEPARATOR "<br>") '
                . 'FROM ('
                . 'Select Tree.Name as TreeName '
                . 'From Org_Tree as Tree ' 
                    . 'inner join Org_Pos as Pos on Pos.OrgTree_FK=Tree.Id '
                . 'Where Tree.OrgUnitType_FK = Rut.OrgUnitType_FK  and Pos.OrgRole_FK = ' .  $this->parentId . ' '
                . 'GROUP BY Tree.Name '
                . 'ORDER BY Tree.Name '
                . ') as TreeName'
            . ') as Occurrency ';
        
        $select = "SELECT *, " ;
        $select.= $this->getTablePathSql();
        $select.=$subSelect . ", ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_UnitType as Typ inner join `Org_Role-UnitType` as Rut on Rut.OrgUnitType_FK = Typ.Id ";
        
        if($id < 0){
            switch ($this->tablePath){
                case TABLE_NAME_UNITTYPE:            
                    $where = "";
                    break;
                case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_UNITTYPE:            
                    $where = "WHERE Rut.OrgUnitType_FK = " . $this->parentId . " ";
                    break;
                default:
                    $where = "";
            }                    
        }
        else{
            $where = "WHERE Rut.Id= " . $id . " ";            
        }
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }
    
    function selectOptions(){
//        $sql = "SELECT 0 as Value, ' Topp' as DisplayText "; 
//        $sql.= "Union "; 
        $select = "SELECT id as Value, Name as DisplayText ";
        $where = "";
//        $where = "WHERE id not in(select OrgUnitType_FK from `Org_Role-UnitType` where OrgRole_FK = " . $this->parentId . ") "; 
//        if(strpos($this->tablePath, TABLE_NAME_ROLE) !== false){
//            $where.=" AND PosEnabled = 2 "; // only units with posenabled list
//        }
        $from = "FROM Org_UnitType ";
        $result = $this->db->select($this->saronUser, $select , $from, $where, "Order by DisplayText ", "", OPTIONS);    
        return $result; 
    }
    
    
    function insert(){
        $this->checkUnitTypeData();
        $sqlInsert = "INSERT INTO Org_UnitType (Name, Description, PosEnabled, SubUnitEnabled, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->name . "', ";
        $sqlInsert.= "'" . $this->description . "', ";
        $sqlInsert.= "'" . $this->posEnabled . "', ";
        $sqlInsert.= "'" . $this->subUnitEnabled . "', ";
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_UnitType", "Id");
        return $this->select($id, RECORD);
    }
    
    
    function update(){
        $this->checkUnitTypeData();
        $update = "UPDATE Org_UnitType ";
        $set = "SET ";        
        $set.= "Name='" . $this->name . "', "; 

        if($this->posEnabled > 0){
            $set.= "PosEnabled='" . $this->posEnabled . "', ";            
        }        

        if($this->subUnitEnabled > 0){
            $set.= "SubUnitEnabled='" . $this->subUnitEnabled . "', ";        
        }

        $set.= "Description='" . $this->description . "', ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from Org_UnitType where Id=" . $this->id);
    }
}
