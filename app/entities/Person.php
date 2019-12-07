<?php

/**
 * Description of Person
 *
 * @author peter
 */
require_once SARON_ROOT . 'app/database/SuperEntity.php';


class Person {
    private $HomeId;
    private $LastName;
    private $FirstName;
    private $DateOfBirth;
    private $Gender;
    private $Email;
    private $Mobile;
    private $DateOfMembershipStart;
    private $MembershipNo;
    private $VisibleInCalendar;
    private $Comment;
       
    function __construct() {
        $this->Id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
        $this->LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
        $this->DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
        $this->DateOfDeath = (String)filter_input(INPUT_POST, "DateOfDeath", FILTER_SANITIZE_STRING);
        $this->PreviousCongregation = (String)filter_input(INPUT_POST, "PreviousCongregation", FILTER_SANITIZE_STRING);
        $this->MembershipNo = (int)filter_input(INPUT_POST, "MembershipNo", FILTER_SANITIZE_NUMBER_INT);
        $this->VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);    
        $this->DateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
        $this->DateOfMembershipEnd = (String)filter_input(INPUT_POST, "DateOfMembershipEnd", FILTER_SANITIZE_STRING);
        $this->NextCongregation = (String)filter_input(INPUT_POST, "NextCongregation", FILTER_SANITIZE_STRING);
        $this->DateOfBaptism = (String)filter_input(INPUT_POST, "DateOfBaptism", FILTER_SANITIZE_STRING);
        $this->Baptister = (String)filter_input(INPUT_POST, "Baptister", FILTER_SANITIZE_STRING);
        $this->CongregationOfBaptism = (String)filter_input(INPUT_POST, "CongregationOfBaptism", FILTER_SANITIZE_STRING);
        $this->CongregationOfBaptismThis = (int)filter_input(INPUT_POST, "CongregationOfBaptismThis", FILTER_SANITIZE_NUMBER_INT);
        $this->Gender = (int)filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_NUMBER_INT);
        $this->Email = (String)filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
        $this->Mobile = (String)filter_input(INPUT_POST, "Mobile", FILTER_SANITIZE_STRING);
        $this->KeyToChurch = (int)filter_input(INPUT_POST, "KeyToChurch", FILTER_SANITIZE_NUMBER_INT);
        $this->KeyToExp = (int)filter_input(INPUT_POST, "KeyToExp", FILTER_SANITIZE_NUMBER_INT);
        $this->Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $this->HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        $this->CommentKey = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
    }
    
    
    function getCurrentHomeId(){
        return $this->HomeId;
    }
    
    function read(){
        return;
    }

    function checkData($db){
        $error = array();
        $error["Result"] = "ERROR";

        if($this->Id === 0){
            $errorMsg = "Tekniskt fel Personen har Id = 0.";            
        }
        
        if($db->exist($this->FirstName, $this->LastName, $this->DateOfBirth, $this->PersonId)){
            $errorMsg = "En person med identitet:<br><b>" . $FirstName . " " . $LastName . " " . $DateOfBirth . "</b><br>finns redan i databasen.";
        }
        
        if(strlen($this->FirstName) === 0 or strlen($this->LastName)==0 or strlen($this->DateOfBirth) === 0){
            $error["Message"] = "Personen behöver ett för- och ett efternamn samt ett födelsedadum för att kunna lagras i registret";
        }

        if(strlen($this->DateOfMembershipStart) === 0 and strlen($this->DateOfMembershipEnd) > 0){
            $error["Message"] = "Personen måste ha ett datum för medlemskapets start om den ska ha ett slutdatum för medlemskapet.";
        }
        
        if($this->MembershipNo < 1 and strlen($this->DateOfMembershipStart) > 0){
            $error["Message"] = "Personen har ett datum för start av medlemskap men saknar medlemsnummer. Lägg till ett medlemsnummer.";
        }

        if($this->VisibleInCalendar === 0 and strlen($this->DateOfMembershipStart) > 0){
            $error["Message"] = "Ange om personen ska vara synlig i adresskalendern eller ej.";
        }

        if($this->VisibleInCalendar === 2){
            if((strlen($this->DateOfMembershipStart) === 0 and strlen($this->DateOfMembershipEnd) === 0) or strlen($this->DateOfMembershipEnd) > 0){
                $error["Message"] = "Endast medlemmar ska vara synliga i adresskalendern.";
            }
        }

        if($this->MembershipNo > 0 and strlen($this->DateOfMembershipStart)===0){
            $error["Message"] = "Personen har inget datum för start av medlemskap men har ett medlemsnummer. Ange en korrekt kombination av uppgifter.";
        }
        
        if(strlen($error["Message"])>0){
            return json_encode($error);
        }
        
        //Adjustments
        if(strlen($this->DateOfDeath) > 0){
            if(strlen($this->DateOfMembershipEnd) === 0 and $this->DateOfMembershipStart > 0){
                $this->DateOfMembershipEnd = $this->DateOfDeath;
            }
            $this->newHomeId = 0;
            $this->Email = null;
            $this->Mobile = null;
        }
        
        if(strlen($this->DateOfMembershipEnd) > 0){    
            $this->VisibleInCalendar = 1;            
        }

        return true;
    }
    
    function checkBaptistData(){
        $error = array();
        $error["Result"] = "ERROR";
        
        if(strlen($this->DateOfBaptism) === 0 and strlen($this->comment) === 0 and $this->CongregationOfBaptismThis > 0){
            $error["Message"] = "Ge en kommentar till varför dopdatum saknas.";
        }    
 
        if(strlen($this->DateOfBaptism)  > 0 and $this->CongregationOfBaptismThis === 0){
            $error["Message"] = "Personen anges inte vara döpt, men har ett dopdatum.";
        } 
 
        if(strlen($this->DateOfBaptism)  === 0 and $this->CongregationOfBaptismThis > 0 and strlen($this->Comment) === 0){
            $error["Message"] = "Ge en kommentar till varför dopdatum saknas.";
        } 
 
        if(strlen($this->CongregationOfBaptism) === 0){
            //$error["Message"] = "Du glömde att ange en dopförsamling.";
        }


        if(strlen($error["Message"])>0){
            return json_encode($error);
        }
        else{
            return true;
        }        
    }
    
    function getInsertSql($homeId, $inserter){
        $sqlInsert = "INSERT INTO People (LastNameEncrypt, FirstNameEncrypt, DateOfBirth, Gender, EmailEncrypt, MobileEncrypt, DateOfMembershipStart, MembershipNo, VisibleInCalendar, CommentEncrypt, Inserter, HomeId) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= $this->getEncryptedSqlString($this->LastName) . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->FirstName) . ", ";
        $sqlInsert.= $this->getSqlDateString($this->DateOfBirth) . ", ";
        $sqlInsert.= $this->Gender . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Email) . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Mobile) . ", ";
        $sqlInsert.= $this->getSqlDateString($this->DateOfMembershipStart) . ", ";
        $sqlInsert.= $this->getZeroToNull($this->MembershipNo) . ", ";
        $sqlInsert.= $this->VisibleInCalendar . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlInsert.= $inserter . ", ";
        $sqlInsert.= $this->getZeroToNull($homeId) . ") ";
 
        return $sqlInsert;
    }

        
    function getUpdatePersonSql(){
        $setUpdate = "UPDATE People SET ";
        $setUpdate.= "LastNameEncrypt=" . $this->getEncryptedSqlString($this->LastName) . ", ";
        $setUpdate.= "FirstNameEncrypt=" . $this->getEncryptedSqlString($this->FirstName) . ", ";
        $setUpdate.= "DateOfBirth=" . $this->getSqlDateString($this->DateOfBirth) . ", ";
        $setUpdate.= "Gender=" . $this->Gender . ", ";
        $setUpdate.= "MobileEncrypt=" . $this->getEncryptedSqlString($this->Mobile) . ", ";
        $setUpdate.= "EmailEncrypt=" . $this->getEncryptedSqlString($this->Email) . ", ";
        $setUpdate.= "DateOfDeath=" . $this->getSqlDateString($this->DateOfDeath) . ", ";        
        $setUpdate.= "HomeId=" . $this->getZeroToNull($this->HomeId) . ", ";
        $setUpdate.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->CommentEncrypt) . " ";
        $sqlUpdate.= "Updater = ". $id . " ";

        return $setUpdate;
    }
    
    
    function getUpdateMembershipSql(){
        $sqlUpdate = "UPDATE People SET ";
        $sqlUpdate.= "PreviousCongregation='" . $this->getSqlString($this->PreviousCongregation)  . "', ";
        $setUpdate.= "DateOfMembershipStart=" . $this->getSqlDateString($this->DateOfMembershipStart) . ", ";         
        $sqlUpdate.= "MembershipNo=" . $this->getZeroToNull($this->MembershipNo)  . ", ";
        $setUpdate.= "VisibleInCalendar=" . $this->VisibleInCalendar() . ", ";
        $setUpdate.= "DateOfMembershipEnd=" . $this->getSqlDateString($this->DateOfMembershipEnd) . ", ";        
        $sqlUpdate.= "NextCongregation='" . $this->getSqlString($this->NextCongregation)  . "', ";
        $setUpdate.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->CommentEncrypt) . ", ";
        $sqlUpdate.= "Updater = ". $this->id . " ";

       return $sqlUpdate;
    }
    
    
    function getUpdateBaptimsSql(){
        $sqlUpdate = "UPDATE People SET ";
        $sqlUpdate.= "PreviousCongregation='" . $this->getSqlString($this->PreviousCongregation)  . "', ";
        $sqlUpdate.= "MembershipNo=" . $this->getZeroToNull($this->MembershipNo)  . ", ";
        $sqlUpdate.= "MembershipNo=" . $this->getZeroToNull($this->MembershipNo)  . ", ";

        return $sqlUpdate;
    }
    
    function getSelectPersonSql(){
        return SQL_STAR_PEOPLE . ", ". DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . DATES_AS_ALISAS_MEMBERSTATES;     
    }
    
    function getSelectBaptistSql(){
        return SQL_STAR_PEOPLE . ", ". DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . DATES_AS_ALISAS_MEMBERSTATES;     
    }

    function getSelectMemberShipSql(){
        return SQL_STAR_PEOPLE . ", ". DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", " . DATES_AS_ALISAS_MEMBERSTATES;     
    }

    function getDeleteSql(){
        return;
    }
    

}
