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
    protected $filter;

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
        $this->filter = new HomesFilter($db, $saronUser);

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
        $TABLE_HOMES_AND_ID = "Homes.Id";    
        
        $sqlSelect = SQL_STAR_HOMES . ", " .  $this->saronUser->getRoleSql(true);         
        $sqlSelect.= $this->getAppCanvasSql(true);
        $sqlSelect.= $this->getHomeSelectSql(ALIAS_CUR_HOMES, $TABLE_HOMES_AND_ID, false);

        $sqlWhere = "WHERE ";

        if($id < 0){            
            $rec = RECORDS;
            switch ($this->appCanvasPath){
                case TABLE_NAME_HOMES:            
                    $sqlWhere.= $this->filter->getHomesFilterSql($this->groupId);
                    $sqlWhere.= $this->filter->getSearchFilterSql($this->uppercaseSearchString);
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



    function getHomeSelectSql($tableAlias, $homesTableNameAndId, $continue){
        $sql = $this->getLongHomeNameSql($tableAlias, "LongHomeName", true);
        $sql.= $this->getFieldSql($tableAlias, "FamilyName", "FamilyNameEncrypt", "", true, true);
        $sql.= $this->getFieldSql($tableAlias, "Address", "AddressEncrypt", "", true, true);
        $sql.= $this->getFieldSql($tableAlias, "Zip", "Zip", "", false, true);
        $sql.= $this->getFieldSql($tableAlias, "City", "City", "", false, true);
        $sql.= $this->getFieldSql($tableAlias, "Country", "Country", "", false, true);
        $sql.= $this->getFieldSql($tableAlias, "Phone", "PhoneEncrypt", "", true, true);
        $sql.= $this->getFieldSql($tableAlias, "Letter", "Letter", "", false, true);
        $sql.= $this->getFieldSql($tableAlias, "HomeId", "Id", "", false, true);
        $sql.= $this->getResidentsSql($tableAlias, "Residents", $homesTableNameAndId, $continue); 
        return $sql;
    }
    
    
    function getResidentsSql($tableAlias, $fieldAlias, $homesTableNameAndId = "Homes.Id", $continue){

        $sql = "(SELECT GROUP_CONCAT(";
        $sql.= $this->getFieldSql($tableAlias . "Res", "", "FirstNameEncrypt", "", true, false);
        $sql.= ", ' ', ";
        $sql.= $this->getFieldSql($tableAlias . "Res", "", "LastNameEncrypt", "", true, false);
        $sql.= ", ' - ', ";
        $sql.= $this->getFieldSql($tableAlias . "Res", "", "MemberStateName", "", false, false);
        $sql.= " SEPARATOR '<BR>') ";
        $sql.= "FROM view_people_memberstate as " . $tableAlias . "Res ";
        $sql.= "where HomeId = ";
        $sql.= $homesTableNameAndId . " "; 

        $sql.= "AND DateOfDeath is null and DateOfAnonymization is null  ";
        $sql.= "order by DateOfBirth) as ";
        
        if(strlen($tableAlias)>0 && $tableAlias !== ALIAS_CUR_HOMES){
            $sql.= $tableAlias . "_";
        }
        
        $sql.= $fieldAlias;

        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;
    }

    function getLongHomeNameSql($tableAlias, $fieldAlias, $continue){
        $sql = "IF(" . $tableAlias . ".Id is null, 'Inget hem', ";
        $sql.= "concat(";
        $sql.= $this->getFieldSql($tableAlias, "", "FamilyNameEncrypt", "", true, false);
        $sql.= ",' (',";
        $sql.= $this->getFieldSql($tableAlias, "", "AddressEncrypt", "Adress saknas", true, false);
        $sql.= ",', ', ";
        $sql.= $this->getFieldSql($tableAlias, "", "City", "Stad saknas", false, false);
        $sql.= ",') ')) as ";
        
        if(strlen($tableAlias)>0 && $tableAlias !== ALIAS_CUR_HOMES){
            $sql.= $tableAlias . "_";
        }
        
        $sql.= $fieldAlias;

        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }

        return $sql;
    }
    
    
    function selectHomesAsOptions(){

        $where ="";
        if($this->HomeId===0){
            $sql = "SELECT 0 as Value, ' Nytt hem' as DisplayText "; 
            $sql.= "Union "; 
            $sql.= "SELECT -1 as Value, '  Inget hem' as DisplayText ";
            $sql.= "Union "; 
            $sql.= "select Id as Value, " . $this->getLongHomeNameSql(ALIAS_CUR_HOMES, "DisplayText", false);
        }
        else{
            $sql.= "select Id as Value, " . $this->getLongHomeNameSql(ALIAS_CUR_HOMES, "DisplayText", false);
            $where = "WHERE Value=" . $this->HomeId;
        }
        $result = $this->db->select($this->saronUser, $sql, "FROM Homes ", $where, "ORDER BY DisplayText ", "", "Options");    
        return $result;
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