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
    
    
    function select($rec = "Records"){
        $tw = new PeopleViews();
        $sqlSelect = $tw->getPeopleViewSql($this->tableview, $this->saronUser);

        $gf = new PeopleFilter();
        $sqlWhere = "WHERE ";       
        $sqlWhere.= $gf->getPeopleFilterSql($this->groupId);
        $sqlWhere.= $gf->getSearchFilterSql($this->uppercaseSearchString);
        $result =  $this->db->select($this->saronUser, $sqlSelect, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, $sqlWhere, $this->getSortSql(), $this->getPageSizeSql(), $rec);
        return $result;
        
    }
    
}
