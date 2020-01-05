<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/PeopleViews.php';
require_once SARON_ROOT . 'app/entities/PeopleFilter.php';


class People extends SuperEntity{

    protected $tableview;
    
    protected $PersonId;
    protected $HomeId;
    protected $LastName;
    protected $FirstName;
    protected $DateOfBirth;
    protected $DateOfDeath;
    protected $Gender;
    protected $Email;
    protected $Mobile;
    protected $DateOfBaptism;
    protected $Baptister;
    protected $CongregationOfBaptism;
    protected $CongregationOfBaptismThis;
    protected $PreviousCongregation;
    protected $DateOfMembershipStart;
    protected $MembershipNo;
    protected $VisibleInCalendar;
    protected $DateOfMembershipEnd;
    protected $NextCongregation;
    protected $KeyToChurch;
    protected $KeyToExp;
    protected $Comment;
    protected $CommentKey;

    protected $home;
    protected $uppercaseSearchString;
    

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
        $this->PersonId = (int)filter_input(INPUT_POST, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        if($this->PersonId === 0){
            $this->PersonId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);
        }
        $this->HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        $this->LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
        $this->FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
        $this->DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
        $this->DateOfDeath = (String)filter_input(INPUT_POST, "DateOfDeath", FILTER_SANITIZE_STRING);
        $this->Gender = (int)filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_NUMBER_INT);
        $this->Email = (String)filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
        $this->Mobile = (String)filter_input(INPUT_POST, "Mobile", FILTER_SANITIZE_STRING);
        $this->DateOfBaptism = (String)filter_input(INPUT_POST, "DateOfBaptism", FILTER_SANITIZE_STRING);
        $this->Baptister = (String)filter_input(INPUT_POST, "Baptister", FILTER_SANITIZE_STRING);
        $this->CongregationOfBaptism = (String)filter_input(INPUT_POST, "CongregationOfBaptism", FILTER_SANITIZE_STRING);
        $this->CongregationOfBaptismThis = (int)filter_input(INPUT_POST, "CongregationOfBaptismThis", FILTER_SANITIZE_NUMBER_INT);
        $this->PreviousCongregation = (String)filter_input(INPUT_POST, "PreviousCongregation", FILTER_SANITIZE_STRING);
        $this->DateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
        $this->MembershipNo = (int)filter_input(INPUT_POST, "MembershipNo", FILTER_SANITIZE_NUMBER_INT);
        $this->VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);    
        $this->DateOfMembershipEnd = (String)filter_input(INPUT_POST, "DateOfMembershipEnd", FILTER_SANITIZE_STRING);
        $this->NextCongregation = (String)filter_input(INPUT_POST, "NextCongregation", FILTER_SANITIZE_STRING);
        $this->KeyToChurch = (int)filter_input(INPUT_POST, "KeyToChurch", FILTER_SANITIZE_NUMBER_INT);
        $this->KeyToExp = (int)filter_input(INPUT_POST, "KeyToExp", FILTER_SANITIZE_NUMBER_INT);
        $this->Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $this->CommentKey = (String)filter_input(INPUT_POST, "CommentKey", FILTER_SANITIZE_STRING);
        $this->tableview = (String)filter_input(INPUT_POST, "tableview", FILTER_SANITIZE_STRING);
    }
    
    
    function select($rec = "Records"){
        $tw = new PeopleViews();
        $sqlSelect = $tw->getPeopleViewSql($this->tableview, $this->saronUser);

        $gf = new PeopleFilter();
        $sqlWhere = "WHERE ";       
        $sqlWhere.= $gf->getPeopleFilterSql($this->groupId);
        $sqlWhere.= $gf->getSearchFilterSql($this->uppercaseSearchString);
        $result =  $this->db->select($this->saronUser, $sqlSelect, SQL_FROM_PEOPLE_LEFT_JOIN_HOMES, $sqlWhere, $this->getSortSql(), $this->getPageSizeSql(), $rec);
        return $result;
        
    }
    
}
