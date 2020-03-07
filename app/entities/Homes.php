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
        switch ($this->selection){
        case "options":
            $this->deleteEmptyHomes(); // clean up
            return $this->selectHomeOptions();       
        default:
            return $this->selectDefault();
        }
    }


    function selectDefault(){
        $filter = new HomesFilter();
        $sqlSelect = SQL_STAR_HOMES . ", " . $this->saronUser->getRoleSql(true) . CONTACTS_ALIAS_RESIDENTS;
        $sqlWhere = "WHERE ";
        $sqlWhere.= $filter->getHomesFilterSql($this->groupId);
        $sqlWhere.= $filter->getSearchFilterSql($this->uppercaseSearchString);
        $result = $this->db->select($this->saronUser, $sqlSelect, "FROM Homes ", $sqlWhere, $this->getSortSql(), $this->getPageSizeSql());
        return $result;        
    }

    function selectHomeOptions(){

        $where ="";
        if($this->HomeId===0){
            $sql = "SELECT 0 as Value, ' Nytt hem' as DisplayText "; 
            $sql.= "Union "; 
            $sql.= "SELECT -1 as Value, '  Inget hem' as DisplayText ";
            $sql.= "Union "; 
            $sql.= "select Id as Value, " . getLongHomeNameSql(ALIAS_CUR_HOMES, "DisplayText", false);
        }
        else{
            $sql.= "select Id as Value, " . getLongHomeNameSql(ALIAS_CUR_HOMES, "DisplayText", false);
            $where = "WHERE Value=" . $this->HomeId;
        }
        $result = $this->db->select($this->saronUser, $sql, "FROM Homes ", $where, "ORDER BY DisplayText ", "", "Options");    
        return $result;
    } 
    
    
    function deleteEmptyHomes(){
        $deleteSql = "delete from Homes where Homes.Id not in (select Homeid from People where HomeId is not null group by HomeId)";
        $this->db->delete($deleteSql);
    }    
}
