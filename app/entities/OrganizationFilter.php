<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/entities/MemberState.php';
require_once SARON_ROOT . 'app/database/queries.php'; 

class OrganizationFilter{

    private $db;
    private $saronUser;
    private $memberState;
    
    function __construct($db, $saronUser){
        $this->db = $db;
        $this->saronUser = $saronUser;
        $this->memberState = new MemberState($db, $saronUser);
    }
    
    
    function getOrganizationFilterSql($groupId){ //Memberstatelogic
        
        switch ($groupId){
            case 0:
                return 'true';
            default :
                return 'true';
                
        }        
    }
    function getSearchFilterSql($uppercaseSearchString){        
        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";
    
            $sqlWhereSearch= "Tree.Description " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "ParentUnitName " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Typ.Name " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Tree.Name " . $sqlWhereSearchSubString . " "; 
            return $sqlWhereSearch;    
        }        
        return "true ";
    }

    
    function getTreeSearchFilterSql($uppercaseSearchString, $field = "Path"){        
        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";
    
            $sqlWhereSearch.= $field . $sqlWhereSearchSubString . " "; 
            return $sqlWhereSearch;    
        }        
        return "true ";
    }
    

    function getPosRoleSearchFilterSql($uppercaseSearchString){        
        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";

            $sqlWhereSearch.= "(Tree.Name " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Pos.Comment " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Role.Name " . $sqlWhereSearchSubString . ") "; 
            return $sqlWhereSearch;    
        }        
        return "true ";
    }
    
    function getRoleSearchFilterSql($uppercaseSearchString){        
        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";

            $sqlWhereSearch.= "(Role.Name " . $sqlWhereSearchSubString . " or "; 
            $sqlWhereSearch.= "Role.Description " . $sqlWhereSearchSubString . ") "; 
            return $sqlWhereSearch;    
        }        
        return "true ";
    }
    
    function getUnitTypeSearchFilterSql($uppercaseSearchString){        
        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";

            $sqlWhereSearch.= "(Typ.Name " . $sqlWhereSearchSubString . " OR "; 
            $sqlWhereSearch.= "Typ.Description " . $sqlWhereSearchSubString . ") "; 
            return $sqlWhereSearch;    
        }        
        return "true ";
    }
    
}
