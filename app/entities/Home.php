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
        $sqlInsert = "INSERT INTO Homes (FamilyNameEncrypt, Inserter, InserterName) VALUES (";
        $sqlInsert.= $this->getEncryptedSqlString($FamilyName) . ", ";
        $sqlInsert.= $this->saronUser->WP_ID . ", '";
        $sqlInsert.= $this->saronUser->userDisplayName . "' ";
        $sqlInsert.= ");";
        $HomeId = $this->db->insert($sqlInsert, ALIAS_CUR_HOMES, "Id", 'Hem', 'Familjenamn', null, $this->saronUser);
        return $HomeId;
    }
    
    
    function update(){
        $answ = $this->checkHomeData();
        if(strpos($answ, "ERROR") !== false){
            return $answ;
        }
        $sqlUpdate = "UPDATE Homes ";
        $sqlSet = "SET ";
        $sqlSet.= "FamilyNameEncrypt = " . $this->getEncryptedSqlString($this->FamilyName) . ", ";
        $sqlSet.= "AddressEncrypt = " . $this->getEncryptedSqlString($this->Address) . ", ";
        $sqlSet.= "PhoneEncrypt = " .  $this->getEncryptedSqlString($this->Phone) . ", ";
        $sqlSet.= "CoEncrypt = " .  $this->getEncryptedSqlString($this->Co) . ", ";
        $sqlSet.= "City = " . $this->getSqlString($this->City) . ", ";
        $sqlSet.= "Zip = " . $this->getSqlString($this->Zip) . ", ";
        $sqlSet.= "Letter = " . $this->Letter . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->WP_ID. ", ";
        $sqlSet.= "UpdaterName = '" . $this->saronUser->userDisplayName . "', ";
        $sqlSet.= "Country = " . $this->getSqlString($this->Country) . " ";     
        $sqlWhere = "WHERE Id=" . $this->id . ";";
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere, 'Homes', 'Id', $this->id, 'Hem','Familjenamn', null, $this->saronUser);
        
        return $this->select($this->id);
    }    

    
    
    function select($_id = -1){
        $id = $this->getId($_id, $this->id);
        $sqlSelect = "SELECT "; 
        $sqlSelect.= $this->saronUser->getRoleSql(true);
        $sqlSelect.= $this->getHomeSelectSql(ALIAS_CUR_HOMES, $id, false);
        
        $result = $this->db->select($this->saronUser, $sqlSelect, "FROM Homes ", "WHERE Id = " . $this->id, "", "", RECORD);
        return $result;        
    }
        
}