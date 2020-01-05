<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/HomesFilter.php';

class Homes extends SuperEntity{
    protected $FamilyName;
    protected $Address;
    protected $Phone;
    protected $Co;
    protected $City;
    protected $Zip;
    protected $Country;
    protected $Letter;
    protected $HomeId;

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
        
        $this->FamilyName = (String)filter_input(INPUT_POST, "FamilyName", FILTER_SANITIZE_STRING);
        $this->Address = (String)filter_input(INPUT_POST, "Address", FILTER_SANITIZE_STRING);
        $this->Phone = (String)filter_input(INPUT_POST, "Phone", FILTER_SANITIZE_STRING);
        $this->Co = (String)filter_input(INPUT_POST, "Co", FILTER_SANITIZE_STRING);
        $this->City = (String)filter_input(INPUT_POST, "City", FILTER_SANITIZE_STRING);
        $this->Zip = (String)filter_input(INPUT_POST, "Zip", FILTER_SANITIZE_STRING);
        $this->Country = (String)filter_input(INPUT_POST, "Country", FILTER_SANITIZE_STRING);
        $this->Letter = (int)filter_input(INPUT_POST, "Letter", FILTER_SANITIZE_NUMBER_INT);
        $this->HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        if($this->HomeId === 0){
            $this->HomeId = (int)filter_input(INPUT_GET, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        }
    }

    
    function select(){
        $sqlWhere = "WHERE ";
        $filter = new HomesFilter();
        $sqlWhere.= $filter->getHomesFilterSql($this->groupId);
        $sqlWhere.= $filter->getSearchFilterSql($this->uppercaseSearchString);
        $result = $this->db->select($this->saronUser, SQL_STAR_HOMES . $this->saronUser->getRoleSql() . ", ". CONTACTS_ALIAS_RESIDENTS, "FROM Homes ", $sqlWhere, $this->getSortSql(), $this->getPageSizeSql());
        return $result;        
    }    
}
