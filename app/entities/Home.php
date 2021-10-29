<?php

require_once SARON_ROOT . 'app/entities/Homes.php';


class Home extends Homes{
        
    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
    }
    
    
    function checkHomeData(){
        $error = array();
        $error["Result"] = "OK";
        $error["Message"] = "";
        
        if(strlen($this->FamilyName) === 0){
            $error["Message"] = "Det måste finnas ett Familjenamn.";
        }

        if(strlen($error["Message"])>0){
            $error["Result"] = "ERROR";
            return json_encode($error);
        }
    }
    
    
    function create($FamilyName){
        $this->FamilyName = $FamilyName;
        $sqlInsert = "INSERT INTO Homes (FamilyNameEncrypt) VALUES ";
        $sqlInsert.= "(" . $this->getEncryptedSqlString($FamilyName) . ");";
        $HomeId = $this->db->insert($sqlInsert, ALIAS_CUR_HOMES, "Id");
        return $HomeId;
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
        $sqlWhere = "WHERE Id=" . $this->id . ";";
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
        
        return $this->select(RECORD);
    }    

    
    
    function select($rec = RECORDS){
        $sqlSelect = "SELECT "; 
        $sqlSelect.= $this->saronUser->getRoleSql(true);
        $sqlSelect.= $this->getHomeSelectSql(ALIAS_CUR_HOMES, $this->HomeId, false);
        
        $result = $this->db->select($this->saronUser, $sqlSelect, "FROM Homes ", "WHERE Id = " . $this->HomeId, "", "", $rec);
        return $result;        
    }
    
    
    function getHomeSelectSql($tableAlias, $homeId, $continue){
        $sql = getLongHomeNameSql($tableAlias, "LongHomeName", true);
        $sql.= getFieldSql($tableAlias, "FamilyName", "FamilyNameEncrypt", "", true, true);
        $sql.= getFieldSql($tableAlias, "Address", "AddressEncrypt", "", true, true);
        $sql.= getFieldSql($tableAlias, "Zip", "Zip", "", false, true);
        $sql.= getFieldSql($tableAlias, "City", "City", "", false, true);
        $sql.= getFieldSql($tableAlias, "Country", "Country", "", false, true);
        $sql.= getFieldSql($tableAlias, "Phone", "PhoneEncrypt", "", true, true);
        $sql.= getFieldSql($tableAlias, "Letter", "Letter", "", false, true);
        $sql.= getFieldSql($tableAlias, "HomeId", "Id", "", false, true);
        $sql.= getResidentsSql($tableAlias, "Residents", $homeId, $continue);   
        return $sql;
    }

}