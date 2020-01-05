<?php

require_once SARON_ROOT . 'app/entities/Homes.php';


class Home extends Homes{
        
    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
    }
    
    
    function checkHomeData(){
        $error = array();
        $error["Result"] = "OK";
        
        if(strlen($familyName) === 0){
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
        $HomeId = $this->db->insert($sqlInsert, "Homes", "Id");
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
        $sqlWhere = "WHERE Id=" . $this->HomeId . ";";
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
        
        return $this->select();
    }
    

    function select(){
        $result = $this->db->select($this->saronUser, SQL_STAR_HOMES . $this->saronUser->getRoleSql() . ", ". ADDRESS_ALIAS_LONG_HOMENAME . ", " . NAMES_ALIAS_RESIDENTS, "FROM Homes ", "WHERE Id = " . $this->HomeId, "", "");
        return $result;        
    }
    
    

}