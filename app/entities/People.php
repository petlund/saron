<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/PeopleViews.php';
require_once SARON_ROOT . 'app/entities/PeopleFilter.php';


class People extends SuperEntity{

    protected $tableview;
    protected $home;
    protected $uppercaseSearchString;

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
        $this->tableview = (String)filter_input(INPUT_POST, "tableview", FILTER_SANITIZE_STRING);
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

        $select = "SELECT null as Value, '-' as DisplayText "; 
        $select.= "Union "; 
        $select.= "select Id as Value, " . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . "as DisplayText ";
        $where = "WHERE " . SQL_WHERE_MEMBER;
        $where = "";
        $result = $this->db->select($this->saronUser, $select, "FROM People ", $where, "ORDER BY DisplayText ", "", "Options");    
        return $result;
    }     
}
