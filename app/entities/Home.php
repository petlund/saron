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
        
    function __construct($db) {
        $this->db=$db;
        $this->FamilyName = (String)filter_input(INPUT_POST, "FamilyName", FILTER_SANITIZE_STRING);
        $this->Address = (String)filter_input(INPUT_POST, "Address", FILTER_SANITIZE_STRING);
        $this->Phone = (String)filter_input(INPUT_POST, "Phone", FILTER_SANITIZE_STRING);
        $this->Co = (String)filter_input(INPUT_POST, "Co", FILTER_SANITIZE_STRING);
        $this->City = (String)filter_input(INPUT_POST, "City", FILTER_SANITIZE_STRING);
        $this->Zip = (String)filter_input(INPUT_POST, "Zip", FILTER_SANITIZE_STRING);
        $this->Country = (String)filter_input(INPUT_POST, "Country", FILTER_SANITIZE_STRING);
        $this->Letter = (int)filter_input(INPUT_POST, "Letter", FILTER_SANITIZE_NUMBER_INT);
    }
    
    function create($FamilyName){
        $sqlInsert = "INSERT INTO Homes (FamilyNameEncrypt) VALUES ";
        $sqlInsert.= "(" . $this->getEncryptedSqlString($FamilyName) . ");";
        $HomeId = $this->db->insert($sqlInsert, "Homes", "Id");
        return $HomeId;
    }
    
    
    function update($db, $HomeId){
        $sqlUpdate = "UPDATE Homes ";
        $sqlSet = "SET ";
        $sqlSet.= "FamilyNameEncrypt = " . $this->getEncryptedSqlString($FamilyName) . ", ";
        $sqlSet.= "AddressEncrypt = " . $this->getEncryptedSqlString($Address) . ", ";
        $sqlSet.= "PhoneEncrypt = " .  $this->getEncryptedSqlString($Phone) . ", ";
        $sqlSet.= "CoEncrypt = " .  $this->getEncryptedSqlString($Co) . ", ";
        $sqlSet.= "City = " . $this->getSqlString($City) . ", ";
        $sqlSet.= "Zip = " . $this->getSqlString($Zip) . ", ";
        $sqlSet.= "Letter = " . $letter . ", ";
        $sqlSet.= "Country = " . $this->getSqlString($Country) . " ";     
        $sqlWhere = "PersonId=" . $HomeId . ";";
        return $db->update($sqlUpdate, $sqlSet, $sqlWhere);
    }
    

    function select($db, $user, $HomeId){
        return $db->selectHome($user, $HomeId, "Records"); 
    }
}