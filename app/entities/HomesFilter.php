<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HomesFilter
 *
 * @author peter
 */
class HomesFilter {
     
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
        $where.= "(Select count(*) from People where Homes.Id=People.HomeId and " . SQL_WHERE_MEMBER . ")>0 ";        
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
//            $sqlWhereSearch.= UPPER(CONVERT(BINARY " . DECRYPTED_FIRSTNAME . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
//            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_LASTNAME . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_FAMILYNAME . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_ADDRESS . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_CO . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
//            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_COMMENT . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
//            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_COMMENT_KEY . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
//            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_EMAIL . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
//            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_MOBILE . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "UPPER(CONVERT(BINARY " . DECRYPTED_PHONE . " USING utf8)) " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "City " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Country " . $sqlWhereSearchSubString . ") "; 
//            $sqlWhereSearch.= "MembershipNo " . $sqlWhereSearchSubString . ") "; 
            return $sqlWhereSearch;
        }        
        return "";
    }
    
}
