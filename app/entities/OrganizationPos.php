<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationPos extends SuperEntity{
    
    private $posId;
    private $posUnitId;
    private $comment;
    private $people_FK;
    private $function_FK;
    private $prevPeople_FK;
    private $orgPosStatus_FK;
    private $orgRole_FK;
    private $orgTree_FK;
    private $orgSuperPos_FK;

    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->posId = (int)filter_input(INPUT_POST, "PosId", FILTER_SANITIZE_NUMBER_INT);
        $this->orgSuperPos_FK = (int)filter_input(INPUT_POST, "OrgSuperPos_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->posUnitId = (int)filter_input(INPUT_POST, "PosUnitId", FILTER_SANITIZE_NUMBER_INT);
        $this->prevPeople_FK = (int)filter_input(INPUT_POST, "PrevPeople_FK", FILTER_SANITIZE_NUMBER_INT);

        $this->comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        
        $this->function_FK = (int)filter_input(INPUT_POST, "Function_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->people_FK = (int)filter_input(INPUT_POST, "People_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->people_FK === 0){
            $this->people_FK = (int)filter_input(INPUT_GET, "People_FK", FILTER_SANITIZE_NUMBER_INT);
        }

        $this->orgPosStatus_FK = (int)filter_input(INPUT_POST, "OrgPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        
        $this->orgTree_FK = (int)filter_input(INPUT_POST, "OrgTree_FK", FILTER_SANITIZE_NUMBER_INT);
        if($this->orgTree_FK === 0){        
            $this->orgTree_FK = (int)filter_input(INPUT_GET, "OrgTree_FK", FILTER_SANITIZE_NUMBER_INT);
        }
    }

    function checkEngagementData(){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "";

        if($this->orgPosStatus_FK === 6 ){
            if($this->people_FK > 0 || !($this->function_FK > 0)){
                $error["Message"] = "Eftersom ett funktionsansvar är angivet ska endast en funktion sättas som ansvarig.";
                throw new Exception(json_encode($error));
            }
        }
        else{
            if($this->orgPosStatus_FK < 3 and $this->people_FK === 0){
                $error["Message"] = "Det saknas ett förslag eller en överenskommelse med någon person.";
                throw new Exception(json_encode($error));
            }
            if($this->orgPosStatus_FK > 2 and $this->people_FK !== 0 ){
                if(str_contains("engagement", $this->tablePath)){
                    $this->people_FK = 0; 
                }
                else{
                    $error["Message"] = "Om positionen inte ska tillsättas eller är vakant ska ingen person kopplas till positionen.";
                    throw new Exception(json_encode($error));
                }
            }
            if($this->function_FK > 0){
                $error["Message"] = "Igen funktion ska anges som ansvarig.";
                throw new Exception(json_encode($error));
            }
        }
    }

     function select(){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectOptions();     // vacant is not hanled yet  
        case RECORDS:
            return $this->selectDefault();       
        case RECORD:
            return $this->selectDefault();       
        default:
            return $this->selectDefault();
        }
    }
    

    
    function selectDefault($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->posId);
        $rec = RECORDS;
         
        $select = "SELECT Pos.*, Tree.ParentTreeNode_FK, Tree.Id as Unit_Id, Role.Name, Role.RoleType, Pos.Id as PosId,  IF(Pos.Updated>Role.updated, Pos.Updated, Role.Updated) as LatestUpdated, ";
        $select.= "(Select SortOrder from `Org_Role-UnitType` as RUT WHERE  RUT.OrgRole_FK = Pos.OrgRole_FK and RUT.OrgUnitType_FK = Tree.OrgUnitType_FK) as SortOrder, ";
        $select.= getPersonSql("pPrev", "PrevPerson", true);
        $select.= $this->getTablePathSql();
        $select.= "IF(Pos.OrgPosStatus_FK = 6, (Select T.Name From Org_Tree as T Where T.Id = Pos.Function_FK), IF(People_FK > 0," . getPersonSql("pCur", null, false) . ", (Select R.Name From Org_Role as R Where R.Id = -People_FK))) as Responsible, ";
        $select.= "IF(Pos.PrevOrgPosStatus_FK = 6, (Select T.Name From Org_Tree as T Where T.Id = Pos.PrevFunction_FK), IF(PrevPeople_FK > 0," . getPersonSql("pPrev", null, false) . ", (Select R.Name From Org_Role as R Where R.Id = -PrevPeople_FK))) as PrevResponsible, ";
        $select.= "Role.Name as RoleName, ";
        $select.= getMemberStateSql("pCur", "MemberState", true);
        $select.= getFieldSql("pCur", "Email", "EmailEncrypt", "", true, true);
        $select.= getFieldSql("pCur", "Mobile", "MobileEncrypt", "", true, true);
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "FROM Org_Pos as Pos inner join Org_Role Role on Pos.OrgRole_FK = Role.Id ";
        $from.= "inner join Org_Tree as Tree on Tree.Id = Pos.OrgTree_FK ";
        $from.= "left outer join People as pCur on pCur.Id = Pos.People_FK ";
        $from.= "left outer join People as pPrev on pPrev.Id = Pos.PrevPeople_FK ";
        
        $where = "";
        if($id < 0){
            switch ($this->tablePath){
                case TABLE_NAME_POS:            
                    $where = "";
                    break;
                case TABLE_NAME_UNITTREE . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_UNITTREE . "/" . TABLE_NAME_UNITTREE . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_ROLE . "/" . TABLE_NAME_UNIT . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_UNIT . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_UNITLIST . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_ENGAGEMENT . "/" . TABLE_NAME_POS:            
                    return $this->selectPersonEngagement();            
                default:
                    $where = "";
            }
        }
        else{
            $where.= "WHERE Pos.Id = " . $id . " ";
            $rec = RECORD;
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }


    
    function selectPersonEngagement(){
        $rec = RECORDS;

        $select = "SELECT *, Pos.Id as PosId, ";
        $select.= "(Select count(*) from Org_Pos as CountPos where CountPos.People_FK = xref.People_FK2) as Cnt, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_Pos as Pos ";
        $from.= "inner join " . ORG_POS_XREF . " on xref.Id =Pos.Id ";
        $where = "WHERE xref.People_FK2 = " . $this->parentId . " and OrgPosStatus_FK < 3 ";
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;        
    }

    
    
    function selectOptions(){
        $sql = "";
        $from = "FROM Org_Pos as Pos inner join Org_Tree as Tree on Pos.OrgTree_FK=Tree.Id ";
        $from.= "inner join Org_UnitType as UnitType on UnitType.Id = Tree.OrgUnitType_FK ";
        $from.= "inner join Org_Role as Role on Role.Id = Pos.OrgRole_FK ";

        $order = "Order by DisplayText ";
        switch ($this->tablePath){
            case TABLE_NAME_ENGAGEMENT . "/" . TABLE_NAME_POS . "/" . SOURCE_LIST:            
                $select.= "SELECT Pos.Id as Value, Concat(Role.Name, ' (', Tree.Name, ". EMBEDDED_SELECT_SUPERPOS . ", ')') as DisplayText ";
                $where = "WHERE People_FK = " . $this->parentId . " "; 
                $sql = $select . $from . $where . $order;
                break;
            case TABLE_NAME_ENGAGEMENT . "/" . TABLE_NAME_POS . "/" . SOURCE_CREATE:            
                $select.= "SELECT Pos.Id as Value, Concat(Role.Name, ' (', Tree.Name, ". EMBEDDED_SELECT_SUPERPOS . ", ')') as DisplayText ";
                $where = "WHERE Pos.OrgPosStatus_FK = 4 and People_FK = 0 or People_FK is null ";
                $sql = $select . $from . $where . $order;
                break;
            default:
                $sql = "Select null as Value, '-' as DisplayText ";
                $sql.= "UNION "; 
                $sql.= "SELECT Pos.Id as Value, Concat(' ', Role.Name, ': ', UnitType.Name, ' ', Tree.Name, ". EMBEDDED_SELECT_SUPERPOS . ") as DisplayText ";
                $sql.= $from;
        }
                
        $result = $this->db->selectSeparate($this->saronUser, $sql, "Select 1",  "Options");    
        return $result; 
    }
    
    
    function insert(){
        $this->checkEngagementData();
        $sqlInsert = "INSERT INTO Org_Pos (People_FK, Function_FK, Comment, OrgPosStatus_FK, OrgSuperPos_FK, OrgRole_FK, OrgTree_FK, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->people_FK . "', ";
        $sqlInsert.= "'" . $this->function_FK . "', ";
        $sqlInsert.= "'" . $this->comment . "', ";
        $sqlInsert.= "'" . $this->orgPosStatus_FK . "', ";
        $sqlInsert.= "null,"; //"'" . $this->orgSuperPos_FK . "', ";
        $sqlInsert.= "'" . $this->orgRole_FK . "', ";
        $sqlInsert.= "'" . $this->orgTree_FK . "', ";
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Pos", "Id");
        
        $result = $this->select($id);
        return $result;
    }
    
    
    function update(){
        $this->checkEngagementData();
        $update = "UPDATE Org_Pos ";
        $set = "SET ";   
        if($this->orgRole_FK > 0){
            $set.= "OrgRole_FK=" . $this->orgRole_FK . ", ";      
        }
        $set.= "Comment='" . $this->comment . "', ";
        $set.= "OrgPosStatus_FK='" . $this->orgPosStatus_FK . "', ";
        $set.= "People_FK=" . $this->people_FK . ", ";
        $set.= "Function_FK=" . $this->function_FK . ", ";
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . " ";
        $where = "WHERE id=" . $this->posId;
        $response = $this->db->update($update, $set, $where);
        
        return $this->select($this->posId);
    }

    
    function addPerson(){
        $this->checkEngagementData();
        $update = "UPDATE Org_Pos ";
        $set = "SET ";   
        $set.= "OrgPosStatus_FK='" . $this->orgPosStatus_FK . "', ";
        $set.= "People_FK=" . $this->people_FK . ", ";
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . " ";
        $where = "WHERE id=" . $this->posId;
        $response = $this->db->update($update, $set, $where);
        
        return $this->select($this->posId);
    }

    
    function delete(){
        return  $this->db->delete("delete from Org_Pos where Id=" . $this->posId);
    }
}
