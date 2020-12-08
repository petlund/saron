<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/queries.php'; 

class PeopleFilter {
 
    function getPeopleFilterSql($groupId){
        switch ($groupId){
            case 0:
                return SQL_WHERE_MEMBER;
            case 1:
                //Dopregister
                return "DateOfMembershipStart is not null and DateOfDeath is null and (" . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "') ";
            case 2:
                //Senast ändrade
                return "true ";
            case 3:
                //Nya medlemmar innevarande år 
                return "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfMembershipStart) ";
            case 4:
                //Nya medlemmar föregående år 
                return "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfMembershipStart) ";
            case 5:
                //Avslutade medlemmar innevarande år 
                return "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfMembershipEnd) ";
            case 6:
                //Avslutade medlemmar föregående år 
                return "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfMembershipEnd) ";
            case 7:
                //Döpta innevarande år
                return "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfBaptism) ";
            case 8:
                //Döpta förgående år
                return "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfBaptism) ";
            case 9:
                //Medlemmar som inte syns i adresskalendern
                return SQL_WHERE_MEMBER . " and VisibleInCalendar != 2 ";
            case 10:
                //Medlemmar som dött
                return "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfDeath) and  DateOfMembershipStart is not null and " . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "' ";
            case 11:
                //Medlemmar som dött föregående år
                return "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfDeath) and DateOfMembershipStart is not null and (" . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "') ";
            case 12:
                //Hela registret
                return "true ";
            case 13:
                //Medlemmar utanför Norrköping
                return SQL_WHERE_MEMBER . " and Zip not like '6%' ";
            case 14:
                //ej medlem
                return "(DateOfMembershipStart is null and DateOfMembershipEnd is null) or ((DateOfMembershipEnd is not null)) and DateOfDeath is null and (" . DECRYPTED_LASTNAME . " NOT like '" . ANONYMOUS . "') ";
            case 15:
                //underlag för anonymisering nästa år
                $sqlWhere = DECRYPTED_LASTNAME . " NOT LIKE '%" . ANONYMOUS . "%' AND DateOfDeath is null AND NOT (";
                $sqlWhere.= "if(DateOfMembershipEnd is null, false, EXTRACT(YEAR FROM DateOfMembershipEnd) = EXTRACT(YEAR FROM Now())) ";
                $sqlWhere.= "OR ";
                $sqlWhere.= "if(DateOfBaptism is null, false, EXTRACT(YEAR FROM DateOfBaptism) = EXTRACT(YEAR FROM Now())) ";
                $sqlWhere.= "OR ";
                $sqlWhere.= "(Select count(*) from Org_Pos Where People_FK = People.Id) > 0 ";
                $sqlWhere.= "OR ";
                $sqlWhere.= "(DateOfMembershipStart is NOT null and DateOfMembershipEnd is null) ";
                $sqlWhere.= ") ";
                return $sqlWhere; 
            case 16:
                //anonymiserade
                return "(" . DECRYPTED_LASTNAME . " like '" . ANONYMOUS . "') ";
            case 17:
                //Medhjälpare
                return "(" . getMemberStateIndexSql("People", null, null) . " = 6) ";
            default :
                return SQL_WHERE_MEMBER; 
        }        
    }
    function getSearchFilterSql($uppercaseSearchString){        
        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";
    
            $sqlWhereSearch = "and (";
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_FIRSTNAME . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_LASTNAME . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_FAMILYNAME . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_ADDRESS . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_CO . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_COMMENT . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_COMMENT_KEY . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_EMAIL . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_MOBILE . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_PHONE . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "PreviousCongregation " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "NextCongregation " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "City " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Country " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "MembershipNo " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "DateOfBirth " . $sqlWhereSearchSubString . ") "; 
            return $sqlWhereSearch;    
        }        
        return "";
    }
}
