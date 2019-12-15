<?php

/**
 * Description of Person
 *
 * @author peter
 */
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/Home.php';
require_once SARON_ROOT . 'app/entities/TableViews.php';
require_once SARON_ROOT . 'app/entities/GroupFilter.php';


class Person extends SuperEntity{
    private $db;
    private $tableview;
    private $groupId;
    
    private $PersonId;
    private $HomeId;
    private $LastName;
    private $FirstName;
    private $DateOfBirth;
    private $DateOfDeath;
    private $Gender;
    private $Email;
    private $Mobile;
    private $DateOfBaptism;
    private $Baptister;
    private $CongregationOfBaptism;
    private $CongregationOfBaptismThis;
    private $PreviousCongregation;
    private $DateOfMembershipStart;
    private $MembershipNo;
    private $VisibleInCalendar;
    private $DateOfMembershipEnd;
    private $NextCongregation;
    private $KeyToChurch;
    private $KeyToExp;
    private $Comment;
    private $CommentKey;

    private $home;

    private $saronUser;

    private $jtPageSize;
    private $jtStartIndex;
    private $jtSorting;
    private $uppercaseSearchString;
    
    
    function __construct($db, $saronUser) {
        parent::__construct();

        $this->db=$db;
        $this->saronUser = $saronUser;
        $this->PersonId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        if($this->PersonId === 0){
            $this->PersonId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        }
        $this->HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        $this->LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
        $this->FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
        $this->DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
        $this->DateOfDeath = (String)filter_input(INPUT_POST, "DateOfDeath", FILTER_SANITIZE_STRING);
        $this->Gender = (int)filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_NUMBER_INT);
        $this->Email = (String)filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
        $this->Mobile = (String)filter_input(INPUT_POST, "Mobile", FILTER_SANITIZE_STRING);
        $this->DateOfBaptism = (String)filter_input(INPUT_POST, "DateOfBaptism", FILTER_SANITIZE_STRING);
        $this->Baptister = (String)filter_input(INPUT_POST, "Baptister", FILTER_SANITIZE_STRING);
        $this->CongregationOfBaptism = (String)filter_input(INPUT_POST, "CongregationOfBaptism", FILTER_SANITIZE_STRING);
        $this->CongregationOfBaptismThis = (int)filter_input(INPUT_POST, "CongregationOfBaptismThis", FILTER_SANITIZE_NUMBER_INT);
        $this->PreviousCongregation = (String)filter_input(INPUT_POST, "PreviousCongregation", FILTER_SANITIZE_STRING);
        $this->DateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
        $this->MembershipNo = (int)filter_input(INPUT_POST, "MembershipNo", FILTER_SANITIZE_NUMBER_INT);
        $this->VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);    
        $this->DateOfMembershipEnd = (String)filter_input(INPUT_POST, "DateOfMembershipEnd", FILTER_SANITIZE_STRING);
        $this->NextCongregation = (String)filter_input(INPUT_POST, "NextCongregation", FILTER_SANITIZE_STRING);
        $this->KeyToChurch = (int)filter_input(INPUT_POST, "KeyToChurch", FILTER_SANITIZE_NUMBER_INT);
        $this->KeyToExp = (int)filter_input(INPUT_POST, "KeyToExp", FILTER_SANITIZE_NUMBER_INT);
        $this->Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $this->CommentKey = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $this->jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $this->jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        $this->jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        $this->tableview = (String)filter_input(INPUT_POST, "tableview", FILTER_SANITIZE_STRING);
        $this->uppercaseSearchString = strtoupper((String)filter_input(INPUT_POST, "searchString", FILTER_SANITIZE_STRING));
        $this->groupId = (int)filter_input(INPUT_POST, "groupId", FILTER_SANITIZE_NUMBER_INT);    

    }
    
    
    function getCurrentHomeId(){
        return $this->HomeId;
    }
    
    function getCurrentPersonId(){
        return $this->PersonId;
    }
    
    function read(){
        return;
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
            $this->home = new Home($this->db, $this->user);
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

    function select($id = -1){
       if($this->PersonId > 0){
           $id=$this->PersonId;
       } 
       
       if($id > 0){
            $sqlSelect = SQL_STAR_PEOPLE . $this->saronUser->getRoleSql() . ", ";
            $sqlSelect.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", ";
            $sqlSelect.= ADDRESS_ALIAS_LONG_HOMENAME . ", ";  
            $sqlSelect.= DECRYPTED_ALIAS_PHONE . ", "; 
            $sqlSelect.= DATES_AS_ALISAS_MEMBERSTATES;
            $sqlWhere = "WHERE People.Id = " . $id;
            return $this->db->select($this->saronUser, $sqlSelect, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, $sqlWhere, "", "");            
       }
       else{
            $tw = new TableViews();
            $sqlSelect = $tw->getTableViewSql($this->tableview, $this->saronUser);
          
            $gf = new GroupFilter();
            $sqlWhere = "WHERE ";       
            $sqlWhere.= $gf->getGroupFilterSql($this->groupId);
            $sqlWhere.= $gf->getSearchFilterSql($this->uppercaseSearchString);
            return $this->db->select($this->saronUser, $sqlSelect, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, $sqlWhere, $this->getSortSql(), $this->getPageSizeSql());
       }
       
    }


    function getSortSql(){
        $sqlOrderBy = ""; 
        if(Strlen($this->jtSorting)>0 and Strlen($this->sqlOrderByLatest)>0){
            $sqlOrderBy = "ORDER BY " . $this->sqlOrderByLatest . ", " . $this->jtSorting . " ";
        }
        else if(Strlen($this->jtSorting)==0 and Strlen($this->sqlOrderByLatest)>0){
            $sqlOrderBy = "ORDER BY " . $this->sqlOrderByLatest . " ";
        }
        else if(Strlen($this->jtSorting)>0 and Strlen($this->sqlOrderByLatest)==0){
            $sqlOrderBy = "ORDER BY " . $this->jtSorting . " ";
        }
        else{
            $sqlOrderBy = "";         
        }
        return $sqlOrderBy;
    }


    function getPageSizeSql(){
        return "LIMIT " . $this->jtStartIndex . ", " . $this->jtPageSize . ";";
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
        $sqlInsert.= "Inserter=" . $this->user->ID . ", ";
        $sqlInsert.= $this->getZeroToNull($this->HomeId) . ") ";
 
        $id = $this->db->insert($sqlInsert, "People", "Id");
        return $this->select($id);
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
        return $this->select($id);
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
        return $this->select($id);

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
        return $this->select($id);
 
    }
   
    
    function setUserRoleInQuery($saronUser){
        $alias = " as user_role ";
        $sql = "'";
        if($saronUser->isEditor()){
            $sql.= SARON_ROLE_EDITOR . "'" . $alias;
        }
        else{
            $sql.= SARON_ROLE_VIEWER . "'" . $alias;            
        }
        return $sql;
    }
    
    
}