<?php

/**
 * Description of Person
 *
 * @author peter
 */
require_once SARON_ROOT . 'app/entities/People.php';
require_once SARON_ROOT . 'app/entities/Home.php';


class Person extends People{

    protected $HomeId;
    protected $OldHomeId;
    protected $LastName;
    protected $FirstName;
    protected $DateOfBirth;
    protected $DateOfDeath;
    protected $Gender;
    protected $Email;
    protected $Mobile;
    protected $DateOfBaptism;
    protected $DateOfFriendshipStart;
    protected $Baptister;
    protected $CongregationOfBaptism;
    protected $CongregationOfBaptismThis;
    protected $PreviousCongregation;
    protected $DateOfMembershipStart;
    protected $MembershipNo;
    protected $VisibleInCalendar;
    protected $DateOfMembershipEnd;
    protected $NextCongregation;
    protected $KeyToChurch;
    protected $KeyToExp;
    protected $Comment;
    protected $CommentKey;
    protected $memberState;
    
    
    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
        $this->memberState = new MemberState($db, $saronUser);
        
        $this->HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        
        $this->OldHomeId = (int)filter_input(INPUT_POST, "OldHomeId", FILTER_SANITIZE_NUMBER_INT);
        if($this->OldHomeId === 0){
            $this->OldHomeId = (int)filter_input(INPUT_GET, "OldHomeId", FILTER_SANITIZE_NUMBER_INT);
        }
        $this->LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
        $this->FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
        $this->DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
        $this->DateOfDeath = (String)filter_input(INPUT_POST, "DateOfDeath", FILTER_SANITIZE_STRING);
        $this->DateOfFriendshipStart = (String)filter_input(INPUT_POST, "DateOfFriendshipStart", FILTER_SANITIZE_STRING);
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
        $this->DateOfAnonymization = (String)filter_input(INPUT_POST, "DateOfAnonymization", FILTER_SANITIZE_STRING);
        $this->NextCongregation = (String)filter_input(INPUT_POST, "NextCongregation", FILTER_SANITIZE_STRING);
        $this->KeyToChurch = (int)filter_input(INPUT_POST, "KeyToChurch", FILTER_SANITIZE_NUMBER_INT);
        $this->KeyToExp = (int)filter_input(INPUT_POST, "KeyToExp", FILTER_SANITIZE_NUMBER_INT);
        $this->Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $this->CommentKey = (String)filter_input(INPUT_POST, "CommentKey", FILTER_SANITIZE_STRING);
        }
    


    function checkPersonData(){//Memberstatelogic
        $error = array();
        $error["Result"] = "OK";
        $error["Message"] = "";

        if(strlen($this->DateOfAnonymization) > 0 ){
            $error["Message"] = "Personen är anonymiserad. Personuppgifter kan därför inte ändras.";
        }
        else if($this->db->exist($this->FirstName, $this->LastName, $this->DateOfBirth, $this->id)){
            $error["Message"] = "En person med identitet:<br><b>" . $this->FirstName . " " . $this->LastName . " " . $this->DateOfBirth . "</b><br>finns redan i databasen.";
        }
        else if(strlen($this->FirstName) === 0 or strlen($this->LastName)==0 or strlen($this->DateOfBirth) === 0){
            $error["Message"] = "Personen behöver ett för- och ett efternamn samt ett födelsedadum för att kunna lagras i registret";
        }
        else if(strlen($this->DateOfDeath) > 0 and $this->HomeId !== -1){
            $error["Message"] = "En avliden person ska inte vara kopplad till något hem.<BR><BR>Välj: 'Inget hem'.";
        }
        

        if(strlen($error["Message"])>0){
            $error["Result"] = "ERROR";
            return json_encode($error);
        }

        
        //Adjustments
        if(strlen($this->DateOfDeath) > 0){
            if(strlen($this->DateOfMembershipEnd) === 0 and strlen($this->DateOfMembershipStart) > 0){
                $this->DateOfMembershipEnd = $this->DateOfDeath;
            }
            
            $this->Email = null;
            $this->Mobile = null;
        }

        if($this->HomeId === 0){
            $this->home = new Home($this->db, $this->saronUser);
            $this->HomeId = $this->home->create($this->LastName);
        }
        
        if(strlen($this->DateOfMembershipEnd) > 0){    
            $this->VisibleInCalendar = 1;            
        }    
        
        return true;
    }
    

    function checkMembershipData(){//Memberstatelogic    
        $error = array();
        $error["Result"] = "OK";
        $error["Message"] = "";

        if(strlen($this->DateOfAnonymization) > 0 ){
            $error["Message"] = "Personen är anonymiserad. Personuppgifter kan därför inte ändras.";
        }
        else if(strlen($this->DateOfMembershipStart) === 0 and strlen($this->DateOfMembershipEnd) > 0){
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


        if(strlen($this->DateOfMembershipStart) > 0 and strlen($this->DateOfMembershipEnd) === 0 and strlen($this->DateOfFriendshipStart) > 0){
            $error["Message"] = "En medlem ska inte ha ett datum för start av vänkontakt.";
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
        $error["Message"] = "";
        
        if(strlen($this->DateOfAnonymization) > 0 ){
            $error["Message"] = "Personen är anonymiserad. Personuppgifter kan därför inte ändras.";
        }
        else if(strlen($this->DateOfBaptism) === 0 and strlen($this->Comment) === 0 and $this->CongregationOfBaptismThis > 0){
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

        if(strlen($error["Message"]) > 0){
            return json_encode($error);
        }
        else{
            return true;
        }        
    }
    
    
    function checkKeyHoldingData(){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "";
        
        if(strlen($this->DateOfAnonymization) > 0 ){
            $error["Message"] = "Personen är anonymiserad. Personuppgifter kan därför inte ändras.";
        }
        else if(($this->KeyToExp === 2 or $this->KeyToChurch === 2) and strlen($this->CommentKey)<5){
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

   
    
    function insert(){
        $sqlInsert = "INSERT INTO People (LastNameEncrypt, FirstNameEncrypt, DateOfBirth, DateOfFriendshipStart, Gender, EmailEncrypt, MobileEncrypt, DateOfMembershipStart, MembershipNo, VisibleInCalendar, Inserter, InserterName, UpdaterName, CommentEncrypt, HomeId) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= $this->getEncryptedSqlString($this->LastName) . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->FirstName) . ", ";
        $sqlInsert.= $this->getSqlDateString($this->DateOfBirth) . ", ";
        $sqlInsert.= $this->getSqlDateString($this->DateOfFriendshipStart) . ", ";
        $sqlInsert.= $this->Gender . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Email) . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Mobile) . ", ";
        $sqlInsert.= $this->getSqlDateString($this->DateOfMembershipStart) . ", ";
        $sqlInsert.= $this->getZeroToNull($this->MembershipNo) . ", ";
        $sqlInsert.= $this->VisibleInCalendar . ", ";
        $sqlInsert.= $this->saronUser->getWP_ID() . ", '";
        $sqlInsert.= $this->saronUser->getDisplayName() . "', '";
        $sqlInsert.= $this->saronUser->getDisplayName() . "', ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlInsert.= $this->getZeroToNull($this->HomeId) . ") ";
 
        $this->id = $this->db->insert($sqlInsert, "People", "Id", 'Person', 'Personid', null, $this->saronUser);
        return $this->select(RECORD);
    }

    
    function update(){
        switch ($this->appCanvasPath){
        case TABLE_NAME_PEOPLE:
            $checkResult = $this->checkPersonData();
            if($checkResult!==true){
                return $checkResult;
            }
            return $this->updatePersonData();
            
        case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE:
            $checkResult = $this->checkPersonData();
            if($checkResult!==true){
                return $checkResult;
            }
            return $this->updatePersonData();
            
        case TABLE_NAME_MEMBER:
            $checkResult = $this->checkMembershipData();
            if($checkResult!==true){
                return $checkResult;
            }
            return $this->updateMembershipData();       
            
        case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_MEMBER:
            $checkResult = $this->checkMembershipData();
            if($checkResult!==true){
                return $checkResult;
            }
            return $this->updateMembershipData();       
            
        case TABLE_NAME_BAPTIST:
            $checkResult=$this->checkBaptistData();
            if($checkResult!==true){
                return $checkResult;
            }
            return $this->updateBaptistData();
            
        case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_BAPTIST:
            $checkResult=$this->checkBaptistData();
            if($checkResult!==true){
                return $checkResult;
            }
            return $this->updateBaptistData();
            
        case TABLE_NAME_KEYS:
            $checkResult = $this->checkKeyHoldingData();
            if($checkResult !== true){
                return $checkResult;
            }                
            return $this->updateKeyHoldning();
            
        case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_KEYS:
            $checkResult = $this->checkKeyHoldingData();
            if($checkResult !== true){
                return $checkResult;
            }                
            return $this->updateKeyHoldning();
            
        case TABLE_NAME_TOTAL:
            return $this->anonymization();       

        default:
            $error = array();
            $error["Result"] = "ERROR";
            $error["Message"] = "Uppdateringen misslyckades. (AppCanvasPath finns inte med i case-satsen.)";
            return json_encode($error);            
        }        
    }
    
    
        
    function updatePersonData(){
        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "LastNameEncrypt=" . $this->getEncryptedSqlString($this->LastName) . ", ";
        $sqlSet.= "FirstNameEncrypt=" . $this->getEncryptedSqlString($this->FirstName) . ", ";
        $sqlSet.= "DateOfBirth=" . $this->getSqlDateString($this->DateOfBirth) . ", ";

        $sqlSet.= "Gender=" . $this->Gender . ", ";
        if($this->VisibleInCalendar > 0 ){
            $sqlSet.= "VisibleInCalendar=" . $this->VisibleInCalendar . ", ";
        }
        $sqlSet.= "MobileEncrypt=" . $this->getEncryptedSqlString($this->Mobile) . ", ";
        $sqlSet.= "EmailEncrypt=" . $this->getEncryptedSqlString($this->Email) . ", ";
        $sqlSet.= "DateOfDeath=" . $this->getSqlDateString($this->DateOfDeath) . ", ";        
        $sqlSet.= "DateOfMembershipEnd=" . $this->getSqlDateString($this->DateOfMembershipEnd) . ", ";        
        $sqlSet.= "HomeId=" . $this->getZeroToNull($this->HomeId) . ", ";
        $sqlSet.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->getWP_ID() . ", ";
        $sqlSet.= "UpdaterName = '" . $this->saronUser->getDisplayName() . "' ";
        $sqlWhere = "where Id=" . $this->id . ";";

        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere, 'People', 'Id', $this->id, 'Person', 'Personid', null, $this->saronUser);
        return $this->selectPersonAfterUpdate($this->id);
    }
    
    
    function updateMembershipData(){
        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "PreviousCongregation=" . $this->getSqlString($this->PreviousCongregation)  . ", ";
        $sqlSet.= "DateOfMembershipStart=" . $this->getSqlDateString($this->DateOfMembershipStart) . ", ";         
        $sqlSet.= "DateOfFriendshipStart=" . $this->getSqlDateString($this->DateOfFriendshipStart) . ", ";
        $sqlSet.= "MembershipNo=" . $this->getZeroToNull($this->MembershipNo)  . ", ";
        $sqlSet.= "VisibleInCalendar=" . $this->VisibleInCalendar . ", ";
        $sqlSet.= "DateOfMembershipEnd=" . $this->getSqlDateString($this->DateOfMembershipEnd) . ", ";        
        $sqlSet.= "NextCongregation=" . $this->getSqlString($this->NextCongregation) . ", ";
        $sqlSet.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->getWP_ID()  . ", ";
        $sqlSet.= "UpdaterName = '" . $this->saronUser->getDisplayName() . "' ";
        $sqlWhere = "where Id=" . $this->id . ";";

        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere, 'People', 'Id', $this->id, 'Person','Personid', null, $this->saronUser);
        return $this->selectPersonAfterUpdate($this->id);

    }
    
    
    function updateBaptistData(){
        $sqlUpdate = "UPDATE People ";
        $sqlSet = "SET ";
        $sqlSet.= "DateOfBaptism=" . $this->getSqlDateString($this->DateOfBaptism)  . ", ";
        $sqlSet.= "BaptisterEncrypt=" . $this->getEncryptedSqlString($this->Baptister)  . ", ";
        $sqlSet.= "CongregationOfBaptism=" . $this->getSqlString($this->CongregationOfBaptism)  . ", ";
        $sqlSet.= "CongregationOfBaptismThis=" . $this->CongregationOfBaptismThis  . ", ";
        $sqlSet.= "CommentEncrypt=" . $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->getWP_ID() . ", ";
        $sqlSet.= "UpdaterName = '" . $this->saronUser->getDisplayName() . "' ";
        $sqlWhere = "where Id=" . $this->id . ";";
        
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere, 'People', 'Id', $this->id, 'Person','Personid', null, $this->saronUser);
        return $this->selectPersonAfterUpdate($this->id);
 
    }
   
    
    function  updateKeyHoldning(){
        $sqlUpdate = "UPDATE People ";  
        $sqlSet = "SET ";
        $sqlSet.= "KeyToChurch=" . $this->KeyToChurch . ", ";
        $sqlSet.= "KeyToExp=" . $this->KeyToExp . ", ";
        $sqlSet.= "CommentKeyEncrypt=" . $this->getEncryptedSqlString($this->CommentKey) . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->getWP_ID() . ", ";
        $sqlSet.= "UpdaterName = '" . $this->saronUser->getDisplayName() . "' ";
        $sqlWhere = "WHERE Id=" . $this->id;
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere,  'People', 'Id', $this->id, 'Person','Personid', null, $this->saronUser);
        return $this->selectPersonAfterUpdate($this->id);
        
    }    
   
    
    function selectPersonAfterUpdate($id){
        $sqlSelect = SELECT_ALL_FIELDS_FROM_VIEW_PEOPLE . ", " . $this->saronUser->getRoleSql(true);
        $sqlSelect.= $this->getAppCanvasSql(true);
        $sqlSelect.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", ";
        $sqlSelect.= $this->OldHomeId . " as OldHomeId ";
        
        $sqlFrom ="FROM view_people as People ";
        
        $sqlWhere = "WHERE ";
        $sqlWhere.= "People.Id = " . $id;
        
        $result =  $this->db->select($this->saronUser, $sqlSelect, $sqlFrom, $sqlWhere, "", "", RECORD);            
        return $result;
    }
 

    function anonymization(){
        $Today = date("Y-m-d") ;
        $select = "Select DateOfMembershipStart, DateOfMembershipEnd, DateOfFriendshipStart, DateOfDeath, DateOfBirth, KeyToExp, KeyToChurch, " . DECRYPTED_ALIAS_COMMENT_KEY ;

        $result = $this->db->select($this->saronUser, $select, " From People ", "Where Id = " . $this->id, "", "");
        $jResults = json_decode($result);
        $jResult = $jResults->Records[0];
            
        if(($jResult->KeyToExp === 2 or $jResult->KeyToChurch === 2) or strlen($jResult->CommentKey)>0){
            $error["Message"] = "Det finns uppgifter om nyckelinnehav. Säkerställ att nycklarna är återlämnade innan anonymiseringen utförs.";
        }
        
        if(strlen($error["Message"])>0){
            $error["Result"] = "ERROR";
            return json_encode($error);
        }

        $sql = "update People set ";
        $sql.= "FirstNameEncrypt = " . $this->getEncryptedSqlString($Today)  . ", ";
        $sql.= "LastNameEncrypt = " . $this->getEncryptedSqlString(ANONYMOUS) . ", ";
        $sql.= "VisibleInCalendar = 0, ";
        $sql.= "EmailEncrypt = NULL, ";
        if(strlen($jResult->DateOfMembershipStart) > 0 and strlen($jResult->DateOfMembershipEnd) === 0){
            $sql.= "DateOfMembershipEnd = '" . $Today . "', ";
        }
        $sql.= "DateOfAnonymization = '" . $Today . "', ";
        $sql.= "MobileEncrypt = NULL, ";
        $sql.= "BaptisterEncrypt = NULL, ";
        $sql.= "CongregationOfBaptism = NULL, ";
        $sql.= "PreviousCongregation = NULL, ";
        $sql.= "NextCongregation = NULL, ";
        $sql.= "CommentEncrypt = NULL, ";
        $sql.= "Updater = ". $this->saronUser->getWP_ID() . ", ";
        $sqlSet.= "Updater = " . $this->saronUser->getWP_ID() . ", ";
        $sqlSet.= "UpdaterName = '" . $this->saronUser->getDisplayName() . "' ";

        $sql.= "HomeId = NULL ";
        $sql.= "where Id=" . $this->id;
        return $this->db->delete($sql, 'People', 'Id', $this->id,  'Person','Personid', '<b>Anonymiserad</b>', $this->saronUser); 
       
    }    
}
