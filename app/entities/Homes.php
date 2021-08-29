<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/HomesFilter.php';

class Homes extends SuperEntity{
    protected $Id;
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
        
        $this->Id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
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


    function checkHomesData(){
        $error = array();
        $error["Result"] = "OK";
        $error["Message"] = "";
        
        if(strlen($this->FamilyName) === 0){
            $error["Message"] = "Det måste finnas ett Familjenamn för hemmet.";
        }

        if(strlen($error["Message"])>0){
            $error["Result"] = "ERROR";
            return json_encode($error);
        }
    }


    
    function select(){
        switch ($this->selection){
        case "options":
            $this->deleteEmptyHomes(); // clean up
            return $this->selectHomesAsOptions();       
        default:
            return $this->selectDefault();
        }
    }


    function selectDefault($idFromCreate = -1){
        $Id = $this->getId($idFromCreate, $this->Id);
            
        $filter = new HomesFilter();
        $sqlSelect = SQL_STAR_HOMES . ", People.HomeId, People.Id as ParentId, " . $this->saronUser->getRoleSql(true) . CONTACTS_ALIAS_RESIDENTS;
        $sqlWhere = "WHERE ";

        if($Id < 0){            
            $rec = RECORDS;
            switch ($this->tablePath){
                case TABLE_NAME_HOMES:            
                    $sqlWhere.= $filter->getHomesFilterSql($this->groupId);
                    $sqlWhere.= $filter->getSearchFilterSql($this->uppercaseSearchString);
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_HOMES:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_HOMES:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                default:
                    $sqlWhere ="";
            }
        }
        else {
            $rec = RECORD;
            $sqlWhere.= "Homes.Id = " . $Id . " ";
        }
        $from = "FROM Homes inner join People on People.HomeId = Homes.Id ";
        $result = $this->db->select($this->saronUser, $sqlSelect, $from, $sqlWhere, $this->getSortSql(), $this->getPageSizeSql(), $rec);
        return $result;        
    }

    function selectHomesAsOptions(){

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
    

    
    function update(){
        $sqlUpdate = "UPDATE Homes ";
        $sqlSet = "SET ";
        $sqlSet.= "FamilyNameEncrypt = " . $this->getEncryptedSqlString($this->FamilyName) . ", ";
        $sqlSet.= "AddressEncrypt = " . $this->getEncryptedSqlString($this->Address) . ", ";
        $sqlSet.= "PhoneEncrypt = " .  $this->getEncryptedSqlString($this->Phone) . ", ";
        $sqlSet.= "CoEncrypt = " .  $this->getEncryptedSqlString($this->Co) . ", ";
        $sqlSet.= "City = " . $this->getSqlString($this->City) . ", ";
        $sqlSet.= "Zip = " . $this->getSqlString($this->Zip) . ", ";
        $sqlSet.= "Letter = " . $this->Letter . ", ";
        $sqlSet.= "Country = " . $this->getSqlString($this->Country) . " ";     
        $sqlWhere = "WHERE Id=" . $this->HomeId . ";";
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
        
        return $this->select(RECORD);
    }    

    
    function deleteEmptyHomes(){
        $oldHomeIdString = "";
        if($this->HomeId > 0){
            "and HomeId is not " . $this->HomeId; 
        }
        $deleteSql = "delete from Homes where Homes.Id not in (select Homeid from People where HomeId is not null " . $oldHomeIdString . " group by HomeId)";
        $this->db->delete($deleteSql);
    }    
}
