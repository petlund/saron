<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/entities/Homes.php';


class PeopleViews {
    private $db;
    private $saronUser;
    private $homes;
    private $memberState;
    
    function __construct($db, $saronUser){
        $this->db = $db;
        $this->saronUser = $saronUser;        
        $this->memberState = new MemberState($db, $saronUser);
        $this->homes = new Homes($db, $saronUser);
    }


    
    function getPeopleViewSql($appCanvasName, $saronUser){
        switch ($appCanvasName){
        case TABLE_NAME_PEOPLE:
            return $this->selectPeople() . ", " . $this->selectNoOfEngagements() . ', ' . $saronUser->getRoleSql(false);
        case TABLE_NAME_STATISTICS:
            return $this->selectPeople() . ", " . $this->selectNoOfEngagements() . ', ' . $saronUser->getRoleSql(false);
        case TABLE_NAME_BIRTHDAY:
            return $this->selectBirthday();
        case TABLE_NAME_MEMBER:
            return SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", "  . $saronUser->getRoleSql(false);
        case TABLE_NAME_BAPTIST:
            return SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", "  . $saronUser->getRoleSql(false);
        case TABLE_NAME_KEYS:
            return "Select People.Id as Id, KeyToExp, KeyToChurch, DateOfBirth, People.UpdaterName, People.Updated, People.InserterName, People.Inserted, " . DECRYPTED_ALIAS_COMMENT_KEY . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . $saronUser->getRoleSql(false);
        case TABLE_NAME_TOTAL:
            return $this->selectTotal() . ", " . $saronUser->getRoleSql(false);
        default:    
            return $this->selectPeople() . ", " . $saronUser->getRoleSql(false);
        }
    }
    
    function selectNoOfEngagements(){
        return "(Select count(*) from Org_Pos where People_FK = People.Id) as Engagement ";
    }
    
    
    function selectPeople(){
        $sql = SQL_STAR_PEOPLE . ", ";
        $sql.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", ";
        $sql.= $this->homes->getLongHomeNameSql(ALIAS_CUR_HOMES, "LongHomeName", true);
        $sql.= DECRYPTED_ALIAS_PHONE;
        return $sql;
    }


    function selectBirthday(){
        $sql = SQL_ALL_FIELDS . ", ";
        $sql.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", ";
        $sql.= "DateOfBirth, ";
        $sql.= "extract(YEAR FROM NOW()) - extract(YEAR FROM DateOfBirth) as Age, ";
        $sql.= "STR_TO_DATE(Concat(extract(year from now()), '-',extract(Month from DateOfBirth),'-',extract(Day from DateOfBirth)),'%Y-%m-%d') as NextBirthday ";
        return $sql;
    }

    function selectTotal(){
        $selectPerson = "select People.Id as Id, concat('<b>'," . DECRYPTED_LASTNAME . ", ' ', " . DECRYPTED_FIRSTNAME . ", '<BR>Född: </b>', DateOfBirth, if(DateOfDeath is null,'', concat (' -- ', DateOfDeath)), '<BR><B>Status: </B>', MemberStateName) as Person, ";

        $selectMember = "concat (";
        $selectMember.= "'<B>Medlemskap start: </B>', if(DateOfMembershipStart is null,'',DateOfMembershipStart), '<BR>', ";
        $selectMember.= "'<B>Medlemskap avslut: </B>', if(DateOfMembershipEnd is null, '',DateOfMembershipEnd ), '<BR>', ";
        $selectMember.= "'<B>Medlemsnummer: </B>', if(MembershipNo is null,'-',MembershipNo), '<BR>', "; 
        $selectMember.= "'<B>Föregående församling: </B>', if(PreviousCongregation is null,'',PreviousCongregation), '<BR>', ";
        $selectMember.= "'<B>Nästa församling: </B>', if(NextCongregation is null,'',NextCongregation), '<BR>', ";
        $selectMember.= "'<B>Not: </B>', if(CommentEncrypt is null,'-'," . DECRYPTED_COMMENT . "), '<BR>' ";
        $selectMember.= ") as Membership, "; 

        $selectBaptist = "concat (";
        $selectBaptist.= "'<B>Dopdatum: </B>', if(DateOfBaptism is null,'',DateOfBaptism), '<BR>', ";
        $selectBaptist.= "'<B>Dopförrättare: </B>', if(BaptisterEncrypt is null,''," . DECRYPTED_BAPTISTER . "), '<BR>', ";
        $selectBaptist.= "'<B>Dopförsamling: </B>', if(CongregationOfBaptism is null,'',CongregationOfBaptism), '<BR>' ";
        $selectBaptist.= ") as Baptist, ";

        $selectAddress = "concat (";
        $selectAddress.= "'<B>','Adress:</B><BR>', ";
        $selectAddress.= "if(" . DECRYPTED_CO . " like '' or " . DECRYPTED_CO . " is null,'',concat('Co ', " . DECRYPTED_CO . ", '<BR>')), ";
        $selectAddress.= "if(AddressEncrypt is null,''," . DECRYPTED_ADDRESS . "), '<BR>', ";
        $selectAddress.= "if(Zip is null,'',concat(Zip, ' ')), ";
        $selectAddress.= "if(City is null,'',City), '<BR>', ";
        $selectAddress.= "if(Country is null,'',Country), '<BR>', ";
        $selectAddress.= FORMATTED_EMAILADDRESS . ", ";
        $selectAddress.= "'<b>Tel: </B>', if(PhoneEncrypt is null,'', " . DECRYPTED_PHONE. "), '<BR>', ";
        $selectAddress.= "'<b>Mobil: </B>', if(MobileEncrypt is null,''," . DECRYPTED_MOBILE . "), '<BR>'";
        $selectAddress.= ") as Contact, ";    

        $selectOther = "concat (";
        $selectOther.= "'<B>Brevutskick: </B>', if(Letter=1,'Ja','Nej'), '<BR>', ";
        $selectOther.= "'<B>Kodad nyckel: </B>', if(KeyToChurch=0,'Nej','Ja'), '<BR>', ";
        $selectOther.= "'<B>Vanlig nyckel: </B>', if(KeyToExp=0,'Nej', 'Ja'), '<BR>', ";
        $selectOther.= "'<B>Kommentar (Nyckel): </B>', if(CommentKeyEncrypt is null, ''," . DECRYPTED_COMMENT_KEY . "), '<BR>', ";
        $selectOther.= "'<B>Synlig i adresskalender: </B>', if(VisibleInCalendar=2,'Ja','Nej'), '<BR>', ";
        $selectOther.= "'<B>Kön: </B>', IF(Gender=0,'-', IF(Gender=1,'Man','Kvinna')) ";
        $selectOther.= ") as Other, ";   

        $selectEngagement = "(Select GROUP_CONCAT(Role.Name,', ', Pos.Comment, ' (', Tree.Name , ". EMBEDDED_SELECT_SUPERPOS . ", ') ',IF(Stat.Id > 1,Concat(' <b style=\"background:yellow;\">[', Stat.Name, ']</b>'),'') SEPARATOR '<br>') as EngagementList "; 

        $selectEngagement = "concat (";
        $selectEngagement.= "'<B>Brevutskick: </B>', if(Letter=1,'Ja','Nej'), '<BR>', ";
        $selectEngagement.= "'<B>Kodad nyckel: </B>', if(KeyToChurch=0,'Nej','Ja'), '<BR>', ";
        $selectEngagement.= "'<B>Vanlig nyckel: </B>', if(KeyToExp=0,'Nej', 'Ja'), '<BR>', ";
        $selectEngagement.= "'<B>Kommentar (Nyckel): </B>', if(CommentKeyEncrypt is null, ''," . DECRYPTED_COMMENT_KEY . "), '<BR>', ";
        $selectEngagement.= "'<B>Synlig i adresskalender: </B>', if(VisibleInCalendar=2,'Ja','Nej'), '<BR>', ";
        $selectEngagement.= "'<B>Kön: </B>', IF(Gender=0,'-', IF(Gender=1,'Man','Kvinna')) ";
        $selectEngagement.= ") as Engagement ";  
           //
        return $selectPerson . $selectMember . $selectBaptist . $selectAddress  . $selectOther . $this->selectNoOfEngagements() . ", People.Inserted, People.InserterName, People.UpdaterName, People.Updated " ;    
    }
    
}
