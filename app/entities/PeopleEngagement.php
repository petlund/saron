<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class PeopleEngagement extends SuperEntity{
    
    private $nodeId;
    private $posId;
    private $orgRole_FK;
    private $orgPosStatus_FK;
    private $orgTreeNode_FK;
    private $people_FK;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->nodeId = (int)filter_input(INPUT_GET, "NodeId", FILTER_SANITIZE_NUMBER_INT);
        $this->posId = (int)filter_input(INPUT_POST, "PosId", FILTER_SANITIZE_NUMBER_INT);
        $this->orgRole_FK = (int)filter_input(INPUT_POST, "OrgRole_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgPosStatus_FK = (int)filter_input(INPUT_POST, "OrgPosStatus_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->orgTreeNode_FK = (int)filter_input(INPUT_POST, "Org_Tree_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->people_FK = (int)filter_input(INPUT_POST, "People_FK", FILTER_SANITIZE_NUMBER_INT);
    }


    
    function select($id = -1, $rec=RECORDS){
        $subSelect = "(Select GROUP_CONCAT(Tree.Name, ': ', Role.Name , IF(Stat.Id > 1,Concat(' <b style=\"background:yellow;\">[', Stat.Name, ']</b>'),'') SEPARATOR '<br>') as SubEngagement ";
        $subFrom = "from Org_Pos as Pos inner join Org_Role as Role on Pos.OrgRole_FK = Role.Id ";
        $subFrom.= "inner join Org_Tree as Tree on Pos.OrgTree_FK = Tree.Id ";
        $subFrom.= "inner join Org_PosStatus as Stat on Stat.Id = Pos.OrgPosStatus_FK ";
        $subWhere = "where Pos.People_FK = p.Id and Stat.Id < 3 "; // Only proposal and committed
        $subGroupBy = "Group by People_FK ";
        $subOrderBy = "Order by SubEngagement) as Engagement, ";
        $subQuery = $subSelect . $subFrom . $subWhere . $subGroupBy . $subOrderBy;
        
        $select = "SELECT p.Id as People_FK, " . getPersonSql(null, "Name", true);
        $select.= getMemberStateSql(null, "MemberState", true);
        $select.= DECRYPTED_ALIAS_EMAIL . ", ";
        $select.= getFieldSql(null, "Mobile", "MobileEncrypt", "", true, true);
        $select.= $subQuery;        
        $select.= "CONCAT(Zip, ' ', City) AS Hosted, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        
        $from = "from People as p left outer join Homes as h on h.id = p.HomeId ";
        
        $where = "WHERE " . SQL_WHERE_MEMBER . " OR p.Id in (Select max(People_FK) from Org_Pos GROUP BY People_FK) ";

        if($id > 0){
            $where.= "WHERE People.Id = " . $id . " ";
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }

}
