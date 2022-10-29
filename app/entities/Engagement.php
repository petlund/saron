<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/PeopleViews.php';
require_once SARON_ROOT . 'app/entities/PeopleFilter.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/entities/MemberState.php';

class Engagement extends SuperEntity{
    private $nodeId;
    private $orgRole_FK;
    private $orgPosStatus_FK;
    private $orgTreeNode_FK;
    private $people_FK;
    private $memberState;
    private $peopleFilter;
    private $meberState; 

 
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        $this->peopleFilter = new PeopleFilter($db, $saronUser);

        $this->memberState = new MemberState($db, $saronUser);

        $this->nodeId = (int)filter_input(INPUT_GET, "NodeId", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgPosStatus_FK = (int)filter_input(INPUT_POST, "OrgPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgTreeNode_FK = (int)filter_input(INPUT_POST, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->people_FK = (int)filter_input(INPUT_POST, "People_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->meberState = new MemberState($db, $saronUser);
    }


    
    function select($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);
        $rec = RECORDS;

        $subSelect1 = "(Select GROUP_CONCAT('<b>', Role.Name, '</b> (', Tree.Name, ') ', "
                        . EMBEDDED_SELECT_SUPERPOS
                        .  "  , ' ', IF(Stat.Id > 1, "
                        . "Concat(' <b style=\"background:yellow;\">[', Stat.Name, ']</b>'),'') SEPARATOR '<br>') as EngagementList "; 
        $subSelect2 = "(select count(*) ";

        $subFrom = "from Org_Pos as Pos inner join Org_Role as Role on Pos.OrgRole_FK = Role.Id ";
        $subFrom.= "inner join Org_Tree as Tree on Pos.OrgTree_FK = Tree.Id ";
        $subFrom.= "inner join Org_PosStatus as Stat on Stat.Id = Pos.OrgPosStatus_FK ";
        $subFrom.= "left outer join (select Pos.Id, Pos.People_FK from Org_Pos as Pos inner join Org_Role as Role on Pos.OrgRole_FK=Role.Id where Role.RoleType=1) as SuperPos on Pos.OrgSuperPos_FK=SuperPos.Id ";

        $subWhere = "where (Pos.People_FK = p.Id or SuperPos.People_FK = p.Id) and Stat.Id < 3 "; // Only proposal and committed
        $subGroupBy ="";    
        $subOrderBy = "Order by EngagementList) as Engagement, ";
        $subQuery1 = $subSelect1 . $subFrom . $subWhere . $subGroupBy . $subOrderBy;
        $subQuery2 = $subSelect2 . $subFrom . $subWhere . $subGroupBy . ") as Cnt, ";
        
        $select = "SELECT p.Id, p.DateOfMembershipStart, MemberStateName, ";
        $select.= $this->getPersonSql(null, "Name", true);
        $select.= DECRYPTED_ALIAS_EMAIL . ", ";
        $select.= $this->getFieldSql(null, "Mobile", "MobileEncrypt", "", true, true);
        $select.= $this->getAppCanvasSql();
        $select.= $subQuery1; 
        $select.= $subQuery2;        
        $select.= "CONCAT(Zip, ' ', City) AS Hosted, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "from view_people_memberstate as p left outer join Homes as h on h.id = p.HomeId ";
        
        $where = "WHERE ";
        if($id > 0){
            $rec=RECORD;
            $where.= "p.Id = " . $id . " ";
        }
        else{
            if($this->groupId === 0){
                $where.= "(p.MemberStateId in (" . PEOPLE_STATE_MEMBERSHIP . ", " . PEOPLE_STATE_FRIEND . ") OR "; 
                $where.= $this->meberState->getHasEngagement("p") . ") ";            
                $where.= $this->peopleFilter->getSearchFilterSql($this->uppercaseSearchString) . " ";            
            }
            else{
                $orgPosStatus_FK=2; //Porposal
                $where.= "((p.MemberStateId NOT in (" . PEOPLE_STATE_MEMBERSHIP . ", " . PEOPLE_STATE_FRIEND . ") AND ";
                $where.= $this->meberState->getHasEngagement("p") . ") ";            
                $where.= $this->peopleFilter->getSearchFilterSql($this->uppercaseSearchString) . ") OR ";            
                $where.= "((p.MemberStateId in (" . PEOPLE_STATE_MEMBERSHIP . ", " . PEOPLE_STATE_FRIEND . ") AND "; 
                $where.= $this->meberState->getHasEngagement("p", $orgPosStatus_FK) . ") ";            
                $where.= $this->peopleFilter->getSearchFilterSql($this->uppercaseSearchString) . ") ";            
            }
            
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }

}
