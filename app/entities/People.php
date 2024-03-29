<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/Homes.php';
require_once SARON_ROOT . 'app/entities/PeopleViews.php';
require_once SARON_ROOT . 'app/entities/PeopleFilter.php';


class People extends SuperEntity{

    protected $personId;
    protected $homes;
    protected $peopleFilter;
    protected $memberState;

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
        $this->homes = new Homes($db, $saronUser);
        $this->memberState = new MemberState($db, $saronUser);
        $this->peopleFilter = new PeopleFilter($db, $saronUser);
        
        $this->personId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        if($this->personId === 0){
            $this->personId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        }
    }
    
    
    function select(){
        switch ($this->resultType){
        case OPTIONS:
            switch ($this->field){
            case "People_FK":
                return $this->selectPeopleOptions();       
            case "MembershipNo":
                return $this->selectNextMembershipNo();       
            }
        case RECORDS:
            switch ($this->appCanvasName){
                case LIST_MOBILE_INSTEAD_OF_EMAIL:
                    return $this->selectMobile();       
                case LIST_EMAIL_MEMBER:
                    return $this->selectEmail(LIST_EMAIL_MEMBER);       
                case LIST_EMAIL_FRIENDSHIP:
                    return $this->selectEmail(LIST_EMAIL_FRIENDSHIP);       
                case LIST_EMAIL_ENDING_FRIENDSHIP:
                    return $this->selectEmail(LIST_EMAIL_ENDING_FRIENDSHIP);       
                case LIST_MOBILE_MEMBER:
                    return $this->selectMobile(LIST_MOBILE_MEMBER);       
                case LIST_MOBILE_FRIENDSHIP:
                    return $this->selectMobile(LIST_MOBILE_FRIENDSHIP);       
                case LIST_MOBILE_ENDING_FRIENDSHIP:
                    return $this->selectMobile(LIST_MOBILE_ENDING_FRIENDSHIP);       
                default:
                    return $this->selectPeople();       
            }
        default:
            return $this->selectPeople();
        }
    }

    
    function selectPeople($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);

        $tw = new PeopleViews($this->db, $this->saronUser);
        $sqlSelect = $tw->getPeopleViewSql($this->appCanvasName, $this->saronUser);
        //$sqlSelect.= $this->homes->getHomeSelectSql(ALIAS_CUR_HOMES, "Homes.Id", false);
        
        if(strlen($this->appCanvasPath) >0){
            $sqlSelect.= ", ";
            $sqlSelect.= $this->getAppCanvasSql(false);
        }
        $sqlWhere = "WHERE ";       
        if($id < 0){
            $rec = RECORDS;     
            switch ($this->appCanvasPath){
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_HOMES:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_BAPTIST:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_MEMBER:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_KEYS:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_PEOPLE . "/" . TABLE_NAME_ENGAGEMENTS:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_HOMES:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_MEMBER:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_BAPTIST:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_KEYS:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                case TABLE_NAME_STATISTICS . "/" . TABLE_NAME_STATISTICS_DETAIL . "/" . TABLE_NAME_PEOPLE . "/" . TABLE_NAME_ENGAGEMENTS:            
                    $sqlWhere.= "People.Id = " . $this->parentId . " ";
                    break;
                default:
                    $sqlWhere.= $this->peopleFilter->getPeopleFilterSql($this->groupId);
                    $sqlWhere.= $this->peopleFilter->getSearchFilterSql($this->uppercaseSearchString);
            }
        }
        else{
            $rec = RECORD;
            if($this->source === SOURCE_CREATE){
                $rec = RECORDS;
            }
            $sqlWhere.= "People.Id = " . $id . " ";
        }
        $sqlFrom = "FROM view_people as People ";
        
        $sort = $this->getSortSql();
        if($this->groupId === 2){
            $sort = "order by Updated desc ";
        }
            
            
        $result =  $this->db->select($this->saronUser, $sqlSelect, $sqlFrom, $sqlWhere, $sort, $this->getPageSizeSql(), $rec);
        return $result;
        
    }
    
    
    function selectNextMembershipNo(){
        $sql = "SELECT 0 as Value, '[Inget medlemsnummer]' as DisplayText, 1 as ind ";

        switch($this->source){

            case SOURCE_EDIT:
                $sql.= "Union "; 
                $sql.= "select MembershipNo as Value, Concat(MembershipNo, ' [Nuvarande]') as DisplayText, 2 as ind From People Where MembershipNo>0 and Id = " . $this->id . " ";
                $sql.= "Union "; 
                $sql.= "select if(max(MembershipNo) is null, 0, max(MembershipNo)) + 1 as Value, CONCAT(if(max(MembershipNo) is null, 0, max(MembershipNo)) + 1, ' [Första lediga]') as DisplayText, 3 as ind ";
                break;
            case SOURCE_CREATE:
                $sql.= "Union "; 
                $sql.= "select if(max(MembershipNo) is null, 0, max(MembershipNo)) + 1 as Value, CONCAT(if(max(MembershipNo) is null, 0, max(MembershipNo)) + 1, ' [Första lediga]') as DisplayText, 3 as ind ";
                break;
            default:
                $sql = "select MembershipNo as Value, MembershipNo as DisplayText";
        }
        $result = $this->db->select($this->saronUser, $sql, "FROM People ", "", "ORDER BY ind ", "", "Options");
        
        return $result;

    }

    
    function selectPeopleOptions(){
        $select = "SELECT null as Value, '-' as DisplayText "; 
        $select.= "Union "; 
        $select.= "select Id as Value, concat(" . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . ", ' (', MemberStateName , ')') as DisplayText ";
        
        $where = "WHERE MemberStateId in (" . PEOPLE_STATE_MEMBERSHIP . ", " . PEOPLE_STATE_FRIEND . ") " ;
        
        $from = "FROM view_people as People ";
        
        $result = $this->db->select($this->saronUser, $select, $from, $where, "ORDER BY DisplayText ", "", "Options");    
        return $result;
    }     
    
    
    
    function selectEmail(){
        $select = "select ";
        $from = " from view_people as People ";
        $where = "where " . DECRYPTED_EMAIL . " like '%@%' ";
        switch($this->appCanvasName){
            case LIST_EMAIL_MEMBER:
                $select.= DECRYPTED_EMAIL . " as entry ";
                $where.= "and MemberStateId = " . PEOPLE_STATE_MEMBERSHIP . " ";
            break;
            case LIST_EMAIL_ENDING_FRIENDSHIP:
                $select.= "Concat(" . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . ", ' - ', " . DECRYPTED_EMAIL . ")  as entry ";
                $where.= "and " . $this->memberState->getIsEndedFriendshipSQL() . " ";
            break;
            case LIST_EMAIL_FRIENDSHIP:
                $select.= DECRYPTED_EMAIL . " as entry ";
                $where.= "and MemberStateId = " . PEOPLE_STATE_FRIEND . " ";
            break;
        }
        $orderby = "group by entry ";
        $orderby.= "order by entry"; 

        $result = $this->db->select($this->saronUser, $select, $from, $where, $orderby, "", RECORDS);    
        return $result;
    }


    function selectMobile(){
        $select = "Select " . DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME_FL . ", " . DECRYPTED_ALIAS_MOBILE . " ";
        $from = "FROM People ";
        switch($this->appCanvasName){
            case LIST_MOBILE_MEMBER:
                $where = "WHERE " .  $this->memberState->hasStateMembershipSQL() . "  "; 
            break;
            case LIST_MOBILE_ENDING_FRIENDSHIP:
                $where = "WHERE " .  $this->memberState->getIsEndedFriendshipSQL() . " "; 
            break;
            case LIST_MOBILE_FRIENDSHIP:
                $where = "WHERE (" .  $this->memberState->hasStateFriendshipSQL() . ") "; 
            break;
        }
        
        $where.= "And (" . DECRYPTED_MOBILE . " is not null) ";
        $where.= "And (" . DECRYPTED_EMAIL . " is null) ";
        $where.= "AND IF(HomeId > 0, (Select count(*) from People as p where People.HomeId=p.HomeId and " . DECRYPTED_EMAIL . " like '%@%') = 0, true) ";      

        $result = $this->db->select($this->saronUser, $select, $from, $where, "", "", RECORDS);    
        return $result;
    }
}
