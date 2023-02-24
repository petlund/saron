<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/queries.php'; 

class HomesFilter {
    private $db;
    private $saronUser;
    private $memberState;

    function __construct($db, $saronUser){
        $this->db = $db;
        $this->saronUser = $saronUser;        
        $this->memberState = new MemberState($db, $saronUser);
    }
    
    
    function getHomesFilterSql($groupId){
        switch ($groupId){
            case 1:
                $sqlWhere = $this->selectHomesWithoutEmail();
                break;
            case 2:
                $sqlWhere = $this->selectHomesWithPaperMailings();
                break;
            case 3:
                $sqlWhere = $this->selectHomesWithMobileOnly();
                break;
            default:
                $sqlWhere = "true ";
        }
        return $sqlWhere;
    }
    
    
    
    function selectHomesWithoutEmail(){
        $where= "(Select count(*) from People where Homes.Id=People.HomeId and " . DECRYPTED_EMAIL . " like '%@%')=0 ";
        $where.= "and ";
        $where.= "(Select count(*) from People where Homes.Id=People.HomeId and CHAR_LENGTH(" . DECRYPTED_MOBILE . ")>5)=0 "; //5 char is not a valid mobile number
        $where.= "and ";
        $where.= "(Select count(*) from People where Homes.Id=People.HomeId and " . $this->memberState->hasStateMembershipSQL() . ")>0 ";        
        return $where;
    }
    
    
    
    function selectHomesWithPaperMailings(){
        $where = "Letter=1 ";        
        return $where;
    }
    

    function getSearchFilterSql($uppercaseSearchString){        
        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";

            $sqlWhereSearch = "and (";
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_FAMILYNAME . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_ADDRESS . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
//            $sqlWhereSearch.= "Residents " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_CO . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_PHONE . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "City " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Country " . $sqlWhereSearchSubString . ") "; 
            return $sqlWhereSearch;
        }        
        return "";
    }
    
}
