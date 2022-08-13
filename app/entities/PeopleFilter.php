<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/entities/MemberState.php';
require_once SARON_ROOT . 'app/database/queries.php'; 

class PeopleFilter{

    private $db;
    private $saronUser;
    private $memberState;
    
    function __construct($db, $saronUser){
        $this->db = $db;
        $this->saronUser = $saronUser;
        $this->memberState = new MemberState($db, $saronUser);
    }
    
    function getPeopleFilterSql($groupId){ //Memberstatelogic
        switch ($groupId){
            case 0:
                return $this->memberState->getIsMemberSQL();
            case 1:
                //Dopregister
                return $this->memberState->getIsBaptistSQL() ;
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
                return $this->memberState->getIsMemberSQL() . " and VisibleInCalendar != 2 ";
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
                return $this->memberState->getIsMemberSQL() . " and Zip not like '6%' ";
            case 14:
                //Församlingens vänner
                return $this->memberState->getIsFriendSQL();
            case 15:
                //ej medlem
                return " NOT " . $this->memberState->getIsMemberSQL();
//                return "((DateOfMembershipStart is null and DateOfMembershipEnd is null) or DateOfMembershipEnd is not null) and DateOfDeath is null and (" . DECRYPTED_LASTNAME . " NOT like '" . ANONYMOUS . "') ";
            case 16:
                //underlag för anonymisering nästa år
                $sqlWhere = DECRYPTED_LASTNAME . " NOT LIKE '%" . ANONYMOUS . "%' AND DateOfDeath is null AND NOT (";
                $sqlWhere.= "if(DateOfMembershipEnd is null, false, EXTRACT(YEAR FROM DateOfMembershipEnd) = EXTRACT(YEAR FROM Now())) ";
                $sqlWhere.= "OR ";
                $sqlWhere.= "if(DateOfBaptism is null, false, EXTRACT(YEAR FROM DateOfBaptism) = EXTRACT(YEAR FROM Now())) ";
                $sqlWhere.= "OR ";
                $sqlWhere.= "if(DateOfFriendshipStart is null, false, DateOfFriendshipStart - Now() > 365) ";
                $sqlWhere.= "OR ";
                $sqlWhere.= "(Select count(*) from Org_Pos Where People_FK = People.Id) > 0 ";
                $sqlWhere.= "OR ";
                $sqlWhere.= "(DateOfMembershipStart is NOT null and DateOfMembershipEnd is null) ";
                $sqlWhere.= ") ";
                return $sqlWhere; 
            case 17:
                //anonymiserade
                return $this->memberState->getIsAnonymizedSQL();
            case 18:
                //Medhjälpare
                return $this->memberState->getIsVolontaireSQL();
            default :
                return $this->memberState->getIsMemberSQL(); 
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
