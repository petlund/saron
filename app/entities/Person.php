<?php

/**
 * Description of Person
 *
 * @author peter
 */
require_once SARON_ROOT . 'app/entities/People.php';
require_once SARON_ROOT . 'app/entities/Home.php';


class Person extends People{
    
    
    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
    }
    
    
    function getCurrentHomeId(){
        return $this->HomeId;
    }
    
    function getCurrentPersonId(){
        return $this->PersonId;
    }
    

    function checkPersonData(){
        $error = array();
        $error["Result"] = "OK";

        if($this->db->exist($this->FirstName, $this->LastName, $this->DateOfBirth, $this->PersonId)){
            $error["Message"] = "En person med identitet:<br><b>" . $this->FirstName . " " . $this->LastName . " " . $this->DateOfBirth . "</b><br>finns redan i databasen.";
        }
        else if(strlen($this->FirstName) === 0 or strlen($this->LastName)==0 or strlen($this->DateOfBirth) === 0){
            $error["Message"] = "Personen behöver ett för- och ett efternamn samt ett födelsedadum för att kunna lagras i registret";
        }
        
        if(strlen($error["Message"])>0){
            $error["Result"] = "ERROR";
            return json_encode($error);
        }
        
        //Adjustments
        if(strlen($this->DateOfDeath) > 0){
            if(strlen($this->DateOfMembershipEnd) === 0 and $this->DateOfMembershipStart > 0){
                $this->DateOfMembershipEnd = $this->DateOfDeath;
            }
            $this->HomeId = 0;
            $this->Email = null;
            $this->Mobile = null;
        }
        
        if(strlen($this->DateOfMembershipEnd) > 0){    
            $this->VisibleInCalendar = 1;            
        }    
        
        if($this->HomeId === -1){
            $this->home = new Home($this->db, $this->saronUser);
            $this->HomeId = $this->home->create($this->LastName);
        }
        return true;
    }
    
    function checkMembershipData(){
        $error = array();
        $error["Result"] = "OK";

        if(strlen($this->DateOfMembershipStart) === 0 and strlen($this->DateOfMembershipEnd) > 0){
            $error["Message"] = "Personen måste ha ett datum för medlemskapets start om den ska ha ett slutdatum för medlemskapet.";
        }
        
        else if($this->MembershipNo < 1 and strlen($this->DateOfMembershipStart) > 0){
            $error["Message"] = "Personen har ett datum för start av medlemskap men saknar medlemsnummer. Lägg till ett medlemsnummer.";
        }

        else if($this->VisibleInCalendar === 0 and strlen($this->DateOfMembershipStart) > 0){
            $error["Message"] = "Ange om personen ska vara synlig i adresskalendern eller ej.";
        }

        else if($this->VisibleInCalendar === 2){
            if((strlen($this->DateOfMembershipStart) === 0 and strlen($this->DateOfMembershipEnd) === 0) or strlen($this->DateOfMembershipEnd) > 0){
                $error["Message"] = "Endast medlemmar ska vara synliga i adresskalendern.";
            }
        }

        if($this->MembershipNo > 0 and strlen($this->DateOfMembershipStart)===0){
            $error["Message"] = "Personen har inget datum för start av medlemskap men har ett medlemsnummer. Ange en korrekt kombination av uppgifter.";
        }
        
        if(strlen($error["Message"])>0){
            $error["Result"] = "ERROR";
            return json_encode($error);
        }
        
        return true;
    }
    
    function checkBaptistData(){
        $error = array();
        $error["Result"] = "ERROR";
        
        if(strlen($this->DateOfBaptism) === 0 and strlen($this->Comment) === 0 and $this->CongregationOfBaptismThis > 0){
            $error["Message"] = "Ge en kommentar till varför dopdatum saknas eller lägg till ett dopdatum.";
        }    
 
        if(strlen($this->DateOfBaptism)  > 0 and $this->CongregationOfBaptismThis === 0){
            $error["Message"] = "Personen anges inte vara döpt, men har ett dopdatum.";
        } 
 
        if(strlen($this->DateOfBaptism)  > 0 and strlen($this->CongregationOfBaptism) === 0 and strlen($this->Comment) === 0 and $this->CongregationOfBaptismThis < 2){
            $error["Message"] = "Ge en kommentar till varför dopförsamling saknas.";
        } 
        
        //Adjustments
        if($this->CongregationOfBaptismThis === 2){
            $this->CongregationOfBaptism = FullNameOfCongregation;
        }

        if(strlen($error["Message"])>0){
            return json_encode($error);
        }
        else{
            return true;
        }        
    }
    
    
    function checkKeyHoldingData(){
        $error = array();
        $error["Result"] = "ERROR";
        
        if(($this->KeyToExp === 2 or $this->KeyToChurch === 2) and strlen($this->CommentKey)<5){
            $error["Message"] = "Du behöver ange en längre kommentar för nyckelinnehavet (Minst 5 tecken).";
        } 

        if(strlen($error["Message"])>0){
            return json_encode($error);
        }
        else{
            return true;
        }
        return true;
    }

    function select($rec = "Records"){
        $sqlSelect = SQL_STAR_PEOPLE . $this->saronUser->getRoleSql() . ", ";
        $sqlSelect.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", ";
        $sqlSelect.= ADDRESS_ALIAS_LONG_HOMENAME . ", ";  
        $sqlSelect.= DECRYPTED_ALIAS_PHONE . ", "; 
        $sqlSelect.= DATES_AS_ALISAS_MEMBERSTATES;
        $sqlWhere = "WHERE People.Id = " . $this->PersonId;
        $result =  $this->db->select($this->saronUser, $sqlSelect, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, $sqlWhere, "", "", $rec);            
        return $result;
    }


    function insert(){
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
        $sqlInsert.= "Inserter=" . $this->saronUser->ID . ", ";
        $sqlInsert.= $this->getZeroToNull($this->HomeId) . ") ";
 
        $this->PersonId = $this->db->insert($sqlInsert, "People", "Id");
        return $this->select("Record");
    }

        
    function updatePersonData(){
        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "LastNameEncrypt=" . $this->getEncryptedSqlString($this->LastName) . ", ";
        $sqlSet.= "FirstNameEncrypt=" . $this->getEncryptedSqlString($this->FirstName) . ", ";
        $sqlSet.= "DateOfBirth=" . $this->getSqlDateString($this->DateOfBirth) . ", ";
        $sqlSet.= "Gender=" . $this->Gender . ", ";
        $sqlSet.= "MobileEncrypt=" . $this->getEncryptedSqlString($this->Mobile) . ", ";
        $sqlSet.= "EmailEncrypt=" . $this->getEncryptedSqlString($this->Email) . ", ";
        $sqlSet.= "DateOfDeath=" . $this->getSqlDateString($this->DateOfDeath) . ", ";        
        $sqlSet.= "HomeId=" . $this->getZeroToNull($this->HomeId) . ", ";
        $sqlSet.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->ID . " ";
        $sqlWhere = "where Id=" . $this->PersonId . ";";

        $id = $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
        return $this->select("Records");
    }
    
    
    function updateMembershipData(){
        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "PreviousCongregation=" . $this->getSqlString($this->PreviousCongregation)  . ", ";
        $sqlSet.= "DateOfMembershipStart=" . $this->getSqlDateString($this->DateOfMembershipStart) . ", ";         
        $sqlSet.= "MembershipNo=" . $this->getZeroToNull($this->MembershipNo)  . ", ";
        $sqlSet.= "VisibleInCalendar=" . $this->VisibleInCalendar . ", ";
        $sqlSet.= "DateOfMembershipEnd=" . $this->getSqlDateString($this->DateOfMembershipEnd) . ", ";        
        $sqlSet.= "NextCongregation=" . $this->getSqlString($this->NextCongregation) . ", ";
        $sqlSet.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->ID  . " ";
        $sqlWhere = "where Id=" . $this->PersonId . ";";

        $id = $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
        return $this->select();

    }
    
    
    function updateBaptistData(){
        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "DateOfBaptism=" . $this->getSqlDateString($this->DateOfBaptism)  . ", ";
        $sqlSet.= "BaptisterEncrypt=" . $this->getEncryptedSqlString($this->Baptister)  . ", ";
        $sqlSet.= "CongregationOfBaptism=" . $this->getSqlString($this->CongregationOfBaptism)  . ", ";
        $sqlSet.= "CongregationOfBaptismThis=" . $this->CongregationOfBaptismThis  . ", ";
        $sqlSet.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->ID . " ";
        $sqlWhere = "where Id=" . $this->PersonId . ";";
        
        $id = $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
        return $this->select();
 
    }
   
    
    function  updateKeyHoldning(){
        $sqlUpdate = "UPDATE People ";  
        $sqlSet = "SET ";
        $sqlSet.= "KeyToChurch=" . $this->KeyToChurch . ", ";
        $sqlSet.= "KeyToExp=" . $this->KeyToExp . ", ";
        $sqlSet.= "CommentKeyEncrypt=" . $this->getEncryptedSqlString($this->CommentKey) . " ";
        $sqlWhere = "WHERE Id=" . $this->getCurrentPersonId();
        $id = $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
        return $this->select();
        
    }
    
    
    function anonymization(){
        $Today = date("Y-m-d") ;
        $result = $this->db->select($this->saronUser, "Select Id ", "From People ", "Where DateOfMembershipEnd is null and Id = " . $this->PersonId, "", "");
        $jResult = json_decode($result);

        $sql = "update People set ";
        $sql.= "FirstNameEncrypt = " . $this->getEncryptedSqlString($Today)  . ", ";
        $sql.= "LastNameEncrypt = " . $this->getEncryptedSqlString(ANONYMOUS) . ", ";
        $sql.= "VisibleInCalendar = 0, ";
        $sql.= "EmailEncrypt = NULL, ";
        if($jResult->TotalRecordCount ==='1'){
            $sql.= "DateOfMembershipEnd = '" . $Today . "', ";
        }
        $sql.= "MobileEncrypt = NULL, ";
        $sql.= "BaptisterEncrypt = NULL, ";
        $sql.= "CongregationOfBaptism = NULL, ";
        $sql.= "PreviousCongregation = NULL, ";
        $sql.= "NextCongregation = NULL, ";
        $sql.= "CommentEncrypt = NULL, ";
        $sql.= "Updater = ". $this->saronUser->ID . ", ";

        $sql.= "HomeId = NULL ";
        $sql.= "where Id=" . $this->PersonId;
        return $this->db->delete($sql); 
        
    }    
}
