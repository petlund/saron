<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/PeopleViews.php';
require_once SARON_ROOT . 'app/entities/PeopleFilter.php';


class People extends SuperEntity{

    protected $tableview;
    protected $home;
    protected $uppercaseSearchString;
    protected $filterType;

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
        $this->tableview = (String)filter_input(INPUT_POST, "tableview", FILTER_SANITIZE_STRING);
        $this->filterType = (String)filter_input(INPUT_GET, "filterType", FILTER_SANITIZE_STRING);
    }
    
    
    function select(){
        switch ($this->selection){
        case "options":
            return $this->selectPeopleOptions();       
        default:
            return $this->selectDefault();
        }
    }


    
    function selectDefault($rec = RECORDS){
        $tw = new PeopleViews();
        $sqlSelect = $tw->getPeopleViewSql($this->tableview, $this->saronUser);

        $gf = new PeopleFilter();
        $sqlWhere = "WHERE ";       
        $sqlWhere.= $gf->getPeopleFilterSql($this->groupId);
        $sqlWhere.= $gf->getSearchFilterSql($this->uppercaseSearchString);
        $result =  $this->db->select($this->saronUser, $sqlSelect, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, $sqlWhere, $this->getSortSql(), $this->getPageSizeSql(), $rec);
        return $result;
        
    }
    
    function selectPeopleOptions(){

        $SELECT_PERSSON = "(SELECT " . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . " FROM People as P inner join Org_Pos as Pos on p.Id = Pos.People_FK WHERE Pos.OrgRole_FK = Role.Id)";
        $SELECT_CONCAT = "concat(' ', Name , ' (', " . $SELECT_PERSSON . ", ')')";
        $SELECT_CONCAT_NULL = "concat(' ', Name , ' (-)')";
        $select = "SELECT 0 as Value, '-' as DisplayText "; 
        $select.= "Union "; 
        $select.= "select -Id as Value, IF(" . $SELECT_CONCAT . " is null, " . $SELECT_CONCAT_NULL . ", " . $SELECT_CONCAT . ") as DisplayText FROM Org_Role as Role WHERE RoleType=1 ";
        $select.= "Union "; 
        $select.= "select Id as Value, " . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . "as DisplayText ";
        if($this->filterType === "member"){
            $where = "WHERE " . SQL_WHERE_MEMBER;
        }
        $result = $this->db->select($this->saronUser, $select, "FROM People ", $where, "ORDER BY DisplayText ", "", "Options");    
        return $result;
    }     
}
