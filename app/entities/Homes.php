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
        switch ($this->resultType){
        case OPTIONS:
            $this->deleteEmptyHomes(); // clean up
            return $this->selectHomesAsOptions();       
        default:
            return $this->selectDefault($idFromCreate = -1);
        }
    }


    function selectDefault($_id){
        $id = $this->getId($_id, $this->id);
            
        $filter = new HomesFilter();
        $sqlSelect = SQL_STAR_HOMES  . ", " .  $this->saronUser->getRoleSql(true);         
        $sqlSelect.= $this->getTablePathSql(true);
        $sqlSelect.= $this->getHomeSelectSql(ALIAS_CUR_HOMES, $this->parentId, true);
        $sqlSelect.= getResidentsSql(ALIAS_CUR_HOMES, "Residents", $this->parentId, false);
        //$sqlSelect.= CONTACTS_ALIAS_RESIDENTS;
        $sqlWhere = "WHERE ";

        if($id < 0){            
            $rec = RECORDS;
            switch ($this->tablePath){
                case TABLE_NAME_HOMES:            
                    $sqlWhere.= $filter->getHomesFilterSql($this->groupId);
                    $sqlWhere.= $filter->getSearchFilterSql($this->uppercaseSearchString);
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_HOMES:            
                    $sqlWhere.= "Homes.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_HOMES:            
                    $sqlWhere.= "Homes.Id = " . $this->parentId . " ";
                    break;
                default:
                    $sqlWhere ="";
            }
        }
        else {
            $rec = RECORD;
            $sqlWhere.= "Homes.Id = " . $id . " ";
        }
        $from = "FROM Homes ";
        $result = $this->db->select($this->saronUser, $sqlSelect, $from, $sqlWhere, $this->getSortSql(), $this->getPageSizeSql(), $rec);
        return $result;        
    }



    function getHomeSelectSql($tableAlias, $id, $continue){
        $sql = getLongHomeNameSql($tableAlias, "LongHomeName", true);
        $sql.= getFieldSql($tableAlias, "FamilyName", "FamilyNameEncrypt", "", true, true);
        $sql.= getFieldSql($tableAlias, "Address", "AddressEncrypt", "", true, true);
        $sql.= getFieldSql($tableAlias, "Zip", "Zip", "", false, true);
        $sql.= getFieldSql($tableAlias, "City", "City", "", false, true);
        $sql.= getFieldSql($tableAlias, "Country", "Country", "", false, true);
        $sql.= getFieldSql($tableAlias, "Phone", "PhoneEncrypt", "", true, true);
        $sql.= getFieldSql($tableAlias, "Letter", "Letter", "", false, true);
        $sql.= getFieldSql($tableAlias, "HomeId", "Id", "", false, true);
        $sql.= getResidentsSql($tableAlias, "Residents", $id, $continue); 
        return $sql;
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
        
        return $this->select($this->Id);
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
