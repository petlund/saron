<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/PeopleViews.php';
require_once SARON_ROOT . 'app/entities/PeopleFilter.php';


class People extends SuperEntity{

    protected $Id;
    protected $uppercaseSearchString;
    protected $filter;

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
        $this->Id = (String)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_STRING);
        $this->filter = (String)filter_input(INPUT_GET, "filter", FILTER_SANITIZE_STRING);
    }
    
    
    function select(){
        switch ($this->selection){
        case "options":
            return $this->selectPeopleOptions();       
        case "email":
            return $this->selectEmail(RECORDS);       
        case "mobileInsteadOfMail":
            return $this->selectMobile(RECORDS);       
        default:
            return $this->selectDefault(RECORDS);
        }
    }


    
    function selectDefault($idFromCreate = -1){
        $Id = $this->getId($idFromCreate, $this->Id);

        $tw = new PeopleViews();
        $sqlSelect = $tw->getPeopleViewSql($this->tableView, $this->saronUser);
        if(strlen($this->tablePath) >0){
            $sqlSelect.= ", ";
            $sqlSelect.= $this->getTablePathSql(false);
        }
        $sqlWhere = "WHERE ";       
        if($Id < 0){
            $rec = RECORDS;
            switch ($this->tablePath){
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_HOMES:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_BAPTIST:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_MEMBER:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                default:
                    $gf = new PeopleFilter();
                    $sqlWhere.= $gf->getPeopleFilterSql($this->groupId);
                    $sqlWhere.= $gf->getSearchFilterSql($this->uppercaseSearchString);
            }
        }
        else{
            $rec = RECORD;
            $sqlWhere.= "People.Id = " . $Id . " ";
        }
        $result =  $this->db->select($this->saronUser, $sqlSelect, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, $sqlWhere, $this->getSortSql(), $this->getPageSizeSql(), $rec);
        return $result;
        
    }
    
    
    
    function selectPeopleOptions(){
        
        $SELECT_PERSSON = "(SELECT " . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . " FROM People as P inner join Org_Pos as Pos on P.Id = Pos.People_FK WHERE Pos.OrgRole_FK = Role.Id) ";
        $SELECT_CONCAT = "concat(' ', Name , ' (', " . $SELECT_PERSSON . ", ')')";
        $SELECT_CONCAT_NULL = "concat(' ', Name , ' (-)')";
        
        $select = "select -Id as Value, IF(" . $SELECT_CONCAT . " is null, " . $SELECT_CONCAT_NULL . ", " . $SELECT_CONCAT . ") as DisplayText FROM Org_Role as Role WHERE RoleType=1 ";//and Role.Id not in (select OrgRole_FK from Org_Pos group by OrgRole_FK) "; //RoleTYpe 1 -> "OrgRole"
        $select.= "Union "; 
        $select.= "SELECT 0 as Value, '-' as DisplayText "; 
        $select.= "Union "; 
        $select.= "select Id as Value, " . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . " as DisplayText ";
        
        
        $where = "";
        $where = "WHERE " . getFilteredMemberStateSql("People", null, false, $this->tablePath);
        
        $from = "FROM People ";
        
        $result = $this->db->select($this->saronUser, $select, $from, $where, "ORDER BY DisplayText ", "", "Options");    
        return $result;
    }     
    
    
    
    function selectEmail(){
        $select = "select ";
        $select.= DECRYPTED_ALIAS_EMAIL;
        $from = " from People ";
        $where = "where " . DECRYPTED_EMAIL . " like '%@%' ";
        $where.= "and " . SQL_WHERE_MEMBER . " ";
        $orderby = "group by Email ";
        $orderby.= "order by Email"; 

        $result = $this->db->select($this->saronUser, $select, $from, $where, $orderby, "", RECORDS);    
        return $result;
    }


    function selectMobile(){
        $select = "Select " . DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME_FL . ", " . DECRYPTED_ALIAS_MOBILE . " ";
        $from = "FROM People ";
        $where = "WHERE " . SQL_WHERE_MEMBER . " and " . DECRYPTED_MOBILE . " is not null and "; 
        $where.= "(Select count(*) from People as p where People.HomeId=p.HomeId and " . DECRYPTED_EMAIL . " like '%@%')  = 0 ";        

        $result = $this->db->select($this->saronUser, $select, $from, $where, "", "", RECORDS);    
        return $result;
    }
}
