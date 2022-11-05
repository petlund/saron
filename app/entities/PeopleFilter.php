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
    
    
    function getMemberStateWhereSQL($memberstates){
        $sqlWhere = "MemberStateId in (";
        for($i=0; $i < count($memberstates); $i++){
            if($i > 0){
                $sqlWhere.= ", ";             
            }
            $sqlWhere.= $memberstates[$i];                 
        }
        $sqlWhere.= ") ";    
        
        return $sqlWhere;
    }
    
    function getPeopleFilterSql($groupId){ //Memberstatelogic
        
        switch ($groupId){
            case 0:
                $memberstates = array(PEOPLE_STATE_MEMBERSHIP);
                return $this->getMemberStateWhereSQL($memberstates);
            case 1:
                //Dopregister
                $memberstates = array(PEOPLE_STATE_MEMBERSHIP, PEOPLE_STATE_ONLY_BAPTIST, PEOPLE_STATE_MEMBERSHIP_ENDED);
                return $this->getMemberStateWhereSQL($memberstates);
                            
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
                $memberstates = array(PEOPLE_STATE_MEMBERSHIP);
                return $this->getMemberStateWhereSQL($memberstates) . " and VisibleInCalendar != 2 ";
            case 10:
                //Medlemmar som dött innevarande år
                $memberstates = array(PEOPLE_STATE_DEAD);
                return $this->getMemberStateWhereSQL($memberstates) . "AND EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfDeath)";
            case 11:
                //Medlemmar som dött föregående år
                $memberstates = array(PEOPLE_STATE_DEAD);
                return $this->getMemberStateWhereSQL($memberstates) . "AND EXTRACT(YEAR FROM NOW()) - 1 =EXTRACT(YEAR FROM DateOfDeath)";
            case 12:
                //Hela registret
                return "true ";
            case 13:
                //Medlemmar utanför Norrköping
                $memberstates = array(PEOPLE_STATE_MEMBERSHIP);
                return $this->getMemberStateWhereSQL($memberstates) . " and Zip not like '6%' ";
            case 14:
                //Församlingens vänner
                $memberstates = array(PEOPLE_STATE_FRIEND);
                return $this->getMemberStateWhereSQL($memberstates);
            case 15:
                //ej medlem
                $memberstates = array(PEOPLE_STATE_FRIEND, PEOPLE_STATE_MEMBERSHIP_ENDED, PEOPLE_STATE_REGISTRATED, PEOPLE_STATE_ONLY_BAPTIST);
                return $this->getMemberStateWhereSQL($memberstates);
            case 16:    
                //underlag för anonymisering nästa år
                return "((MemberStateId = " . PEOPLE_STATE_MEMBERSHIP_ENDED . " AND EXTRACT(YEAR FROM DateOfMembershipEnd) < EXTRACT(YEAR FROM Now())) OR " . 
                       "(MemberStateId = " . PEOPLE_STATE_FRIENDSHIP_ENDED . ") OR " . 
                       "(MemberStateId = " . PEOPLE_STATE_REGISTRATED . " AND EXTRACT(YEAR FROM People.Inserted) < EXTRACT(YEAR FROM Now())) OR " . 
                       "(MemberStateId = " . PEOPLE_STATE_ONLY_BAPTIST . " AND EXTRACT(YEAR FROM DateOfBaptism) < EXTRACT(YEAR FROM Now()))) "; 
            case 17:
                //anonymiserade
                $memberstates = array(PEOPLE_STATE_ANONYMiZED);
                return $this->getMemberStateWhereSQL($memberstates);
            case 20:
                //Församlingens vänner
                $memberstates = array(PEOPLE_STATE_REGISTRATED);
                return $this->getMemberStateWhereSQL($memberstates);
            default :
                $memberstates = array(PEOPLE_STATE_MEMBERSHIP);
                return $this->getMemberStateWhereSQL($memberstates);
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
