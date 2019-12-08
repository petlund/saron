<?php

/**
 * Description of Home
 *
 * @author peter
 */

require_once SARON_ROOT . 'app/entities/SuperEntity.php';

class Home extends SuperEntity{
    private $FamilyName;
    private $Address;
    private $Phone;
    private $Co;
    private $City;
    private $Zip;
    private $Country;
    private $Letter;
    private $db;
    private $HomeId;
    private $user;
        
    function __construct($db, $user) {
        //parent::__construct();

        $this->db=$db;
        $this->user=$user;
        $this->FamilyName = (String)filter_input(INPUT_POST, "FamilyName", FILTER_SANITIZE_STRING);
        $this->Address = (String)filter_input(INPUT_POST, "Address", FILTER_SANITIZE_STRING);
        $this->Phone = (String)filter_input(INPUT_POST, "Phone", FILTER_SANITIZE_STRING);
        $this->Co = (String)filter_input(INPUT_POST, "Co", FILTER_SANITIZE_STRING);
        $this->City = (String)filter_input(INPUT_POST, "City", FILTER_SANITIZE_STRING);
        $this->Zip = (String)filter_input(INPUT_POST, "Zip", FILTER_SANITIZE_STRING);
        $this->Country = (String)filter_input(INPUT_POST, "Country", FILTER_SANITIZE_STRING);
        $this->Letter = (int)filter_input(INPUT_POST, "Letter", FILTER_SANITIZE_NUMBER_INT);
        $this->HomeId = (int)filter_input(INPUT_GET, "HomeId", FILTER_SANITIZE_NUMBER_INT);
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
        $result = $this->db->select($this->user, SQL_STAR_HOMES . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . NAMES_ALIAS_RESIDENTS, "FROM Homes ", "WHERE Id = " . $this->HomeId, "", "");
        return $result;
        
    }
}