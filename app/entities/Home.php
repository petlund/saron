<?php

/**
 * Description of Home
 *
 * @author peter
 */

require_once SARON_ROOT . 'app/database/SuperEntity.php';

class Home extends SuperEntity{
        private $FamilyName;
        private $Address;
        private $Phone;
        private $Co;
        private $City;
        private $Zip;
        private $Country;
        private $Letter;

        
    function __construct() {
        $this->FamilyName = (String)filter_input(INPUT_POST, "FamilyName", FILTER_SANITIZE_STRING);
        $this->Address = (String)filter_input(INPUT_POST, "Address", FILTER_SANITIZE_STRING);
        $this->Phone = (String)filter_input(INPUT_POST, "Phone", FILTER_SANITIZE_STRING);
        $this->Co = (String)filter_input(INPUT_POST, "Co", FILTER_SANITIZE_STRING);
        $this->City = (String)filter_input(INPUT_POST, "City", FILTER_SANITIZE_STRING);
        $this->Zip = (String)filter_input(INPUT_POST, "Zip", FILTER_SANITIZE_STRING);
        $this->Country = (String)filter_input(INPUT_POST, "Country", FILTER_SANITIZE_STRING);
        $this->Letter = (int)filter_input(INPUT_POST, "Letter", FILTER_SANITIZE_NUMBER_INT);
    }
    
    function getSetSql(){
        $sqlSet = "SET ";
        $sqlSet.= "FamilyNameEncrypt = AES_ENCRYPT('" . salt() . $familyName . "', " . PKEY . "), ";
        
        if(strlen($address)>0){
            $sqlSet.= "AddressEncrypt = AES_ENCRYPT('" . salt() . $address . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "AddressEncrypt = null, ";            
        }
        
        if(strlen($phone)>0){
            $sqlSet.= "PhoneEncrypt = AES_ENCRYPT('" . salt() . $phone . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "PhoneEncrypt = null, ";            
        }
        if(strlen($co)>0){
            $sqlSet.= "CoEncrypt = AES_ENCRYPT('" . salt() . $co . "', " . PKEY . "), ";
        }
        else{
            $sqlSet.= "CoEncrypt = null, ";            
        }
        
        $sqlSet.= "City='" . $city . "', ";
        $sqlSet.= "Zip='" . $zip . "', ";
        $sqlSet.= "Letter='" . $letter . "', ";
        $sqlSet.= "Country='" . $country . "' ";        
    }
}