<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/PeopleViews.php';
require_once SARON_ROOT . 'app/entities/PeopleFilter.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class Engagement extends SuperEntity{
    
    private $nodeId;
    private $posId;
    private $orgRole_FK;
    private $orgPosStatus_FK;
    private $orgTreeNode_FK;
    private $people_FK;
    protected $tableview;
    protected $uppercaseSearchString;
    protected $filterType;
 
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->tableview = (String)filter_input(INPUT_POST, "tableview", FILTER_SANITIZE_STRING);
        $this->filterType = (String)filter_input(INPUT_GET, "filterType", FILTER_SANITIZE_STRING);

        $this->nodeId = (int)filter_input(INPUT_GET, "NodeId", FILTER_SANITIZE_NUMBER_INT);
        $this->posId = (int)filter_input(INPUT_POST, "PosId", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgPosStatus_FK = (int)filter_input(INPUT_POST, "OrgPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgTreeNode_FK = (int)filter_input(INPUT_POST, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->people_FK = (int)filter_input(INPUT_POST, "People_FK", FILTER_SANITIZE_NUMBER_INT);
    }


    
    function select($id = -1, $rec=RECORDS){
        $subSelect = "(Select GROUP_CONCAT(Tree.Name, ': ', Role.Name , ". EMBEDDED_SELECT_SUPERPOS . ", IF(Stat.Id > 1,Concat(' <b style=\"background:yellow;\">[', Stat.Name, ']</b>'),'') SEPARATOR '<br>') as SubEngagement ";
        $subFrom = "from Org_Pos as Pos inner join Org_Role as Role on Pos.OrgRole_FK = Role.Id ";
        $subFrom.= "inner join Org_Tree as Tree on Pos.OrgTree_FK = Tree.Id ";
        $subFrom.= "inner join Org_PosStatus as Stat on Stat.Id = Pos.OrgPosStatus_FK ";
        $subFrom.= "inner join " . ORG_POS_XREF . " on xref.Id = Pos.Id ";
        $subWhere = "where xref.People_FK2 = p.Id and Stat.Id < 3 "; // Only proposal and committed
        $subGroupBy = "Group by People_FK2 ";
        $subOrderBy = "Order by SubEngagement) as Engagement, ";
        $subQuery = $subSelect . $subFrom . $subWhere . $subGroupBy . $subOrderBy;
        
        $select = "SELECT p.Id, " . getPersonSql(null, "Name", true);
        $select.= getMemberStateSql(null, "MemberState", true);
        $select.= DECRYPTED_ALIAS_EMAIL . ", ";
        $select.= getFieldSql(null, "Mobile", "MobileEncrypt", "", true, true);
        $select.= $subQuery;        
        $select.= "CONCAT(Zip, ' ', City) AS Hosted, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "from People as p left outer join Homes as h on h.id = p.HomeId ";

        $gf = new PeopleFilter();
        $where = "WHERE (" . SQL_WHERE_MEMBER . " OR p.Id in (Select max(People_FK) from Org_Pos GROUP BY People_FK)) ";
        $where.= $gf->getSearchFilterSql($this->uppercaseSearchString);
        

        if($id > 0){
            $where.= "WHERE People.Id = " . $id . " ";
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }

}
