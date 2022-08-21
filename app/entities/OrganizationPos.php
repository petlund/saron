<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationPos extends SuperEntity{
    
    private $comment;
    private $people_FK;
    private $function_FK;
    private $prevPeople_FK;
    private $orgPosStatus_FK;
    private $orgRole_FK;
    private $orgTree_FK;
    private $orgSuperPos_FK;
    private $memberState;

    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        $this->memberState = new MemberState($db, $saronUser);        
        $this->orgSuperPos_FK = (int)filter_input(INPUT_POST, "OrgSuperPos_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->prevPeople_FK = (int)filter_input(INPUT_POST, "PrevPeople_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $this->function_FK = (int)filter_input(INPUT_POST, "Function_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->people_FK = (int)filter_input(INPUT_POST, "People_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgPosStatus_FK = (int)filter_input(INPUT_POST, "OrgPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgTree_FK = (int)filter_input(INPUT_POST, "OrgTree_FK", FILTER_SANITIZE_NUMBER_INT);
    }

    function checkEngagementData(){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "";

        if($this->orgPosStatus_FK < 3){
            if($this->people_FK < 1 and $this->function_FK < 1 and $this->orgSuperPos_FK < 1){
                $error["Message"] = "Det saknas ett förslag eller en överenskommelse med någon person.";
                throw new Exception(json_encode($error));
            }
        }
        if($this->orgPosStatus_FK > 2){
            if($this->appCanvasPath === TABLE_NAME_ENGAGEMENT . "/" . TABLE_NAME_ENGAGEMENTS){
                $this->people_FK=null;
            }
            if($this->people_FK > 0 or $this->function_FK > 0 or $this->orgSuperPos_FK > 0){
                $error["Message"] = "Ingen funktion, roll eller person ska anges som ansvarig.";
                throw new Exception(json_encode($error));
            }
        }
    }

     function select($id = -1){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectOptions();     // vacant is not handled yet  
        default:    
            return $this->selectDefault($id);
        }
    }
    

    
    function selectDefault($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);
        $rec = RECORDS;
        $prevTooltipString = "<div class=\"tooltip\"><u>"; 
        $midTooltipString = "</u><span class=\"tooltiptext\">";
        $postTooltipString = "</span></div>";

        $statusSql = "'<BR>Rollen är: ', (Select Name from Org_PosStatus as Stat Where Id = P.OrgPosStatus_FK) ";
        
        $subSelectCur = "(case "
                    . "WHEN Pos.Function_FK > 0 THEN (Select concat('" . $prevTooltipString . "', F.Name, '" . $midTooltipString . "','Funktionsansvar','" . $postTooltipString . "') From Org_Tree as F Where F.Id = Pos.Function_FK) "
                    . "WHEN Pos.OrgSuperPos_FK > 0 THEN (Select concat('" . $prevTooltipString . "', R.Name, '" . $midTooltipString . "'," . $this->getPersonSql("pCur2", null, false) . "," . $statusSql . ",'" . $postTooltipString . "') From Org_Pos as P inner join Org_Role as R on R.Id=P.OrgRole_FK left outer join People as pCur2 on pCur2.Id=P.People_FK Where P.Id = Pos.OrgSuperPos_FK ) "
                    . "ELSE CONCAT(" . $this->getPersonSql("pCur", null, false) . ", ' (', pCur.MemberStateName, ')') "
                . "end) as Responsible , ";

        $subSelectPrev = "(case "
                    . "WHEN Pos.PrevFunction_FK > 0 THEN (Select concat('" . $prevTooltipString . "', F.Name, '" . $midTooltipString . "','Funktionsansvar','" . $postTooltipString . "') From Org_Tree as F Where F.Id = Pos.Function_FK) "
                    . "WHEN Pos.PrevOrgSuperPos_FK > 0 THEN (Select concat('" . $prevTooltipString . "', R.Name, '" . $midTooltipString . "'," . $this->getPersonSql("pPrev2", null, false) . ",'" . $postTooltipString . "') From Org_Pos as P inner join Org_Role as R on R.Id=P.OrgRole_FK left outer join view_people_memberstate as pPrev2 on pPrev2.Id=P.PrevPeople_FK Where P.Id = Pos.PrevOrgSuperPos_FK) "
                    . "ELSE CONCAT(" . $this->getPersonSql("pPrev", null, false) . ", ' (', pPrev.MemberStateName, ')') "
                . "end) as PrevResponsible , ";

        $subSelectCurIndex = "(case "
                    . "WHEN Pos.Function_FK > 0 THEN 3 "
                    . "WHEN Pos.OrgSuperPos_FK > 0 THEN 2 " 
                    . "ELSE 1 "
                . "end) as ResourceType , ";

        
        
        $select = "SELECT Pos.*, Tree.ParentTreeNode_FK, Role.Name, Role.RoleType, Pos.Id,  IF(Pos.Updated>Role.updated, Pos.Updated, Role.Updated) as LatestUpdated, ";
        $select.= $subSelectCurIndex;
        $select.= "(Select SortOrder from `Org_Role-UnitType` as RUT WHERE  RUT.OrgRole_FK = Pos.OrgRole_FK and RUT.OrgUnitType_FK = Tree.OrgUnitType_FK) as SortOrder, ";
        $select.= $this->getAppCanvasSql();
        $select.= $subSelectCur;
        $select.= $subSelectPrev;
        $select.= "Role.Name as RoleName, ";
        $select.= $this->parentId . " as ParentId, ";
        $select.= $this->getFieldSql("pCur", "MemberStateName", "MemberStateName", "", false, true);
        $select.= $this->getFieldSql("pCur", "Email", "EmailEncrypt", "", true, true);
        $select.= $this->getFieldSql("pCur", "Mobile", "MobileEncrypt", "", true, true);
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "FROM Org_Pos as Pos inner join Org_Role Role on Pos.OrgRole_FK = Role.Id ";
        $from.= "inner join Org_Tree as Tree on Tree.Id = Pos.OrgTree_FK ";
        $from.= "left outer join view_people_memberstate as pCur on pCur.Id = Pos.People_FK ";
        $from.= "left outer join view_people_memberstate as pPrev on pPrev.Id = Pos.PrevPeople_FK ";
        $from.= "left outer join (select Pos.Id, Pos.People_FK from Org_Pos as Pos inner join Org_Role as Role on Pos.OrgRole_FK=Role.Id where Role.RoleType=1) as SuperPos on SuperPos.Id = Pos.OrgSuperPos_FK ";        
        
        $where = "";
        if($id < 0){
            switch ($this->appCanvasPath){
                case TABLE_NAME_UNITTREE . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_UNITTREE . "/" . TABLE_NAME_UNIT . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_UNITLIST . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_ROLE . "/" . TABLE_NAME_UNIT . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_ROLE . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgRole_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_UNITTYPE . "/" . TABLE_NAME_UNIT . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_UNITLIST . "/" . TABLE_NAME_UNIT . "/" . TABLE_NAME_POS:            
                    $where.= "WHERE OrgTree_FK = " . $this->parentId . " ";            
                    break;
                case TABLE_NAME_ENGAGEMENT . "/" . TABLE_NAME_ENGAGEMENTS:    
                    $where = "WHERE Pos.People_FK = ". $this->parentId . " Or SuperPos.People_FK = ". $this->parentId . " "; 
                    //return $this->selectPersonEngagement();            
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_ENGAGEMENTS:            
                    $where = "WHERE pCur.Id = ". $this->parentId . " "; 
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_ENGAGEMENTS:            
                    $where = "WHERE pCur.Id = ". $this->parentId . " "; 
                    break;
                default:
//                    $where = "Where Pos.Function_FK >0 ";
                    //$where.="WHERE OrgSuperPos_FK in (190, 442) or Pos.Id in (190, 442) ";
            }
        }
        else{
            $where.= "WHERE Pos.Id = " . $id . " ";
            $rec = RECORD;
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }
    
    
    function selectOptions(){
        $sql = "";

        $select = "SELECT Pos.Id as Value, Concat(Role.Name, ' (', Tree.Name, ". EMBEDDED_SELECT_SUPERPOS . ", ')') as DisplayText ";

        $from = "FROM Org_Pos as Pos inner join Org_Tree as Tree on Pos.OrgTree_FK=Tree.Id ";
        $from.= "inner join Org_UnitType as UnitType on UnitType.Id = Tree.OrgUnitType_FK ";
        $from.= "inner join Org_Role as Role on Role.Id = Pos.OrgRole_FK ";

        $order = "Order by DisplayText ";
        
        switch ($this->field){
            case "Id":
            switch ($this->source){
                case SOURCE_EDIT:            
                    $where = "WHERE People_FK = " . $this->parentId . " "; 
                    $sql = $select . $from . $where . $order;
                break;
                case SOURCE_CREATE:            
                    $where = "WHERE Pos.OrgPosStatus_FK = 4 AND (People_FK < 1 or People_FK is null) ";
                    $sql = $select . $from . $where . $order;
                break;
                default:
                    $sql = "Select null as Value, '-' as DisplayText ";
                    $sql.= "UNION "; 
                    $sql.= "SELECT Pos.Id as Value, Concat(' ', Role.Name, ' (', Tree.Name, ". EMBEDDED_SELECT_SUPERPOS . ", ')') as DisplayText ";
                    $sql.= $from;
                break;
            }
            break;
            case "OrgSuperPos_FK":
                $where = "WHERE Role.RoleType = 1 ";
                $sql = $select . $from . $where . $order;
            break;
        }
            
        $result = $this->db->selectSeparate($this->saronUser, $sql, "Select 1",  "Options");    
        return $result; 
    }
    
    
    function insertNewPos(){
        $this->checkEngagementData();
        $sqlInsert = "INSERT INTO Org_Pos (People_FK, Function_FK, Comment, OrgPosStatus_FK, OrgSuperPos_FK, OrgRole_FK, OrgTree_FK, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->people_FK . "', ";
        $sqlInsert.= "'" . $this->function_FK . "', ";
        $sqlInsert.= "'" . $this->comment . "', ";
        $sqlInsert.= "'" . $this->orgPosStatus_FK . "', ";
        $sqlInsert.= "'" . $this->orgSuperPos_FK . "', ";
        $sqlInsert.= "'" . $this->orgRole_FK . "', ";
        $sqlInsert.= "'" . $this->parentId . "', ";  //ONLY OrgTree_FK
        $sqlInsert.= "'" . $this->saronUser->WP_ID . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Pos", "Id");
        
        $result = $this->select($id);
        return $result;
    }
    
    function insert(){
        switch ($this->appCanvasPath) {
        case TABLE_NAME_ENGAGEMENT . "/" . TABLE_NAME_ENGAGEMENTS:
            $this->people_FK = $this->parentId;
            $result = $this->addPerson();   
            break;
        default: 
            $result = $this->insertNewPos();
        }
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
        $set.= "OrgPosStatus_FK=" . $this->orgPosStatus_FK . ", ";
        
        if($this->people_FK > 0){
            $set.= "People_FK=" . $this->people_FK . ", ";
        }
        else{
            $set.= "People_FK=null, ";            
        }
        if($this->function_FK > 0){
            $set.= "Function_FK=" . $this->people_FK . ", ";
        }
        else{
            $set.= "Function_FK=null, ";            
        }
        if($this->orgSuperPos_FK >0){
            $set.= "OrgSuperPos_FK=" . $this->orgSuperPos_FK . ", ";
        }
        else{
            $set.= "OrgSuperPos_FK=null, ";            
        }
        if($this->function_FK > 0){
            $set.= "Function_FK=" . $this->function_FK . ", ";
        }
        else{
            $set.= "Function_FK=null, ";            
        }
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . " ";
        $where = "WHERE Id=" . $this->id;
        $response = $this->db->update($update, $set, $where);
        
        return $this->select($this->id);
    }

    
    function addPerson(){
        $this->checkEngagementData();
        $update = "UPDATE Org_Pos ";
        $set = "SET ";   
        $set.= "OrgPosStatus_FK='" . $this->orgPosStatus_FK . "', ";
        $set.= "People_FK=" . $this->people_FK . ", ";
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . " ";
        $where = "WHERE Id=" . $this->id;
        $response = $this->db->update($update, $set, $where);
                
        return $this->select($this->id);
    }

    
    function delete(){
        return  $this->db->delete("delete from Org_Pos where Id=" . $this->id);
    }
}
