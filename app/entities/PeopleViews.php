<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/queries.php'; 

class PeopleViews {
    
    function getPeopleViewSql($tableview, $saronUser){
        switch ($tableview){
        case "people":
            return $this->selectPeople() . ", " . $saronUser->getRoleSql(false);
        case "birthdays":
            return $this->selectBirthday();
        case "member":
            return SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", " . $saronUser->getRoleSql(false);
        case "baptist":
            return SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES .  ", " . $saronUser->getRoleSql(false);
        case "keys":
            return "Select People.Id as PersonId, KeyToExp, KeyToChurch, DateOfBirth, " . DECRYPTED_ALIAS_COMMENT_KEY . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES  . ", " . $saronUser->getRoleSql(false);
        case "total":
            return $this->selectTotal() . ", " . $saronUser->getRoleSql(false);
        default:    
            return $this->selectPeople() . ", " . $saronUser->getRoleSql(false);
        }
    }
    
    
    function selectPeople(){
        $sql = SQL_STAR_PEOPLE . ", ";
        $sql.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", ";
        $sql.= getLongHomeNameSql(ALIAS_CUR_HOMES, "LongHomeName", true);
        $sql.= DECRYPTED_ALIAS_PHONE . ", ";
        $sql.= DATES_AS_ALISAS_MEMBERSTATES;
        return $sql;
    }


    function selectBirthday(){
        $sql = SQL_STAR_PEOPLE . ", ";
        $sql.= "concat(";
        $sql.= "IF(" . FORMATTED_EMAILADDRESS . " like '', '', " . FORMATTED_EMAILADDRESS . "), ";
        $sql.= "IF(" . DECRYPTED_MOBILE . " is null, '', concat(" . DECRYPTED_MOBILE . ", ', ')), ";
        $sql.= "IF(" . DECRYPTED_PHONE . " is null, '', concat(" . DECRYPTED_PHONE . ", ', ')), ";
        $sql.= "IF(" . DECRYPTED_ADDRESS . " is null, ' ', concat(" . DECRYPTED_ADDRESS . ", ', ')), "; 
        $sql.= "IF(" . Zip. " is null, ' ', concat(" . Zip . ", ', ')), ";
        $sql.= "IF(" . City . " is null, ' ', " . City . ")"; 
        $sql.= ") as Contact, ";
        $sql.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", ";
        $sql.= "extract(YEAR FROM NOW()) - extract(YEAR FROM DateOfBirth) as Age, ";
        $sql.= "concat(extract(year from now()),'-', DATE_FORMAT( STR_TO_DATE(extract(Month from DateOfBirth), '%m' ) , '%m' ),'-',DATE_FORMAT(STR_TO_DATE(extract(day from DateOfBirth), '%d' ) , '%d' )) as NextBirthday ";
        return $sql;
    }

    function selectTotal(){
        $selectPerson = "select People.Id as PersonId, concat('<b>'," . DECRYPTED_LASTNAME . ", ' ', " . DECRYPTED_FIRSTNAME . ", '<BR>Född: </b>', DateOfBirth, if(DateOfDeath is null,'', concat (' -- ', DateOfDeath)), '<BR><B>Status: </B>', " . DATES_AS_MEMBERSTATES. ") as Person, ";
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
        $selectOther.= "'<B>Kyrknyckel: </B>', if(KeyToChurch=0,'Nej','Ja'), '<BR>', ";
        $selectOther.= "'<B>Kyrknyckel (Exp): </B>', if(KeyToExp=0,'Nej', 'Ja'), '<BR>', ";
        $selectOther.= "'<B>Kommentar (Nyckel): </B>', if(CommentKeyEncrypt is null, ''," . DECRYPTED_COMMENT_KEY . "), '<BR>', ";
        $selectOther.= "'<B>Synlig i adresskalender: </B>', if(VisibleInCalendar=2,'Ja','Nej'), '<BR>', ";
        $selectOther.= "'<B>Kön: </B>', IF(Gender=0,'-', IF(Gender=1,'Man','Kvinna')) ";
        $selectOther.= ") as Other ";   
           //
        return $selectPerson . $selectMember . $selectBaptist . $selectAddress  . $selectOther;    
    }
    
}
