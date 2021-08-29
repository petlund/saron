<?php
require_once "config.php";
require_once SARON_ROOT . "app/util/GlobalConstants_php.php";

// From config.php
// System
if (!isset($saron)){
    $saron = new stdClass();
}
$saron->uri->wp = WP_URI;
$saron->uri->saron = SARON_URI;
$saron->uri->images = SARON_IMAGES_URI;
$saron->name->full_name = FullNameOfCongregation;
$saron->session->expires_time = SESSION_EXPIRES;
$saron->responsetype->record = RECORD;
$saron->responsetype->records = RECORDS;
$saron->responsetype->options = OPTIONS;
$saron->source->list = SOURCE_LIST;
$saron->source->edit = SOURCE_EDIT;
$saron->source->create = SOURCE_CREATE;
$saron->table->news->viewid = '#NEWS';
$saron->table->nexs->name = 'NEWS';

$saron->table->baptist->viewid = "#" . TABLE_VIEW_BAPTIST ;
$saron->table->baptist->name = TABLE_NAME_BAPTIST ;

$saron->table->people->viewid = "#" . TABLE_VIEW_PEOPLE;
$saron->table->people->name = TABLE_NAME_PEOPLE;

$saron->table->member->viewid = "#" . TABLE_VIEW_MEMBER ;
$saron->table->member->name = TABLE_NAME_MEMBER ;

$saron->table->homes->viewid = "#" . TABLE_VIEW_HOMES ;
$saron->table->homes->name = TABLE_NAME_HOMES ;

$saron->table->keys->viewid = "#" . TABLE_VIEW_KEYS ;
$saron->table->keys->name = TABLE_NAME_KEYS ;

$saron->table->birthday->viewid = "#" . TABLE_VIEW_BIRTHDAY ;
$saron->table->birthday->name = TABLE_NAME_BIRTHDAY ;

$saron->table->total->viewid = "#" . TABLE_VIEW_TOTAL ;
$saron->table->total->name = TABLE_NAME_TOTAL ;

$saron->table->efk->viewid = "#" . TABLE_VIEW_EFK ;
$saron->table->efk->name = TABLE_NAME_EFK ;

$saron->table->statistics->viewid = "#" . TABLE_VIEW_STATISTICS ;
$saron->table->statistics->name = TABLE_NAME_STATISTICS ;

$saron->table->statistics_detail->viewid = "#" . TABLE_VIEW_STATISTICS_DETAIL ;
$saron->table->statistics_detail->name = TABLE_NAME_STATISTICS_DETAIL ;

$saron->graph->statistics->viewid = "#" . GRAPH_VIEW_STATISTICS ;
$saron->graph->statistics->name = GRAPH_NAME_STATISTICS ;

$saron->table->role->viewid = "#" . TABLE_VIEW_ROLE ;
$saron->table->role->name = TABLE_NAME_ROLE ;

$saron->table->engagement->viewid = "#" . TABLE_VIEW_ENGAGEMENT ;
$saron->table->engagement->name = TABLE_NAME_ENGAGEMENT ;

$saron->table->pos->viewid = "#" . TABLE_VIEW_POS ;
$saron->table->pos->name = TABLE_NAME_POS ;

$saron->table->unittype->viewid = "#" . TABLE_VIEW_UNITTYPE ;
$saron->table->unittype->name = TABLE_NAME_UNITTYPE ;

$saron->table->unit->viewid = "#" . TABLE_VIEW_UNIT ;
$saron->table->unit->name = TABLE_NAME_UNIT ;

$saron->table->unitlist->viewid = "#" . TABLE_VIEW_UNITLIST ;
$saron->table->unitlist->name = TABLE_NAME_UNITLIST ;

$saron->table->unittree->viewid = "#" . TABLE_VIEW_UNITTREE ;
$saron->table->unittree->name = TABLE_NAME_UNITTREE ;

$saron->table->orgversion->viewid = "#" . TABLE_VIEW_ORGVERSION ;
$saron->table->orgversion->name = TABLE_NAME_ORGVERSION ;

$saron->table->org_role_status->viewid = "#" . TABLE_VIEW_ORG_ROLE_STATUS ;
$saron->table->org_role_status->name = TABLE_NAME_ORG_ROLE_STATUS ;

$saron->table->users->viewid = "#" . TABLE_VIEW_USERS ;
$saron->table->users->name = TABLE_NAME_USERS ;



$saronJSON = json_encode($saron);


//

    //Organisation
//    echo "<script>" .
//
//        "const TABLE_VIEW_ROLE = '" .  "#" . TABLE_VIEW_ROLE . "';" .
//        "const TABLE_NAME_ROLE = '" . TABLE_NAME_ROLE . "';" .
//
//        "const TABLE_VIEW_ENGAGEMENT = '" .  "#" . TABLE_VIEW_ENGAGEMENT . "';" .
//        "const TABLE_NAME_ENGAGEMENT = '" . TABLE_NAME_ENGAGEMENT . "';" .
//
//        "const TABLE_VIEW_POS = '" .  "#" . TABLE_VIEW_POS . "';" .
//        "const TABLE_NAME_POS = '" . TABLE_NAME_POS . "';" .
//
//        "const TABLE_VIEW_ROLE_UNITTYPE = '" . "#" . TABLE_VIEW_ROLE_UNITTYPE . "';" .    
//        "const TABLE_NAME_ROLE_UNITTYPE = '" . TABLE_NAME_ROLE_UNITTYPE . "';" .
//
//        "const TABLE_VIEW_UNITTYPE = '" .  "#" . TABLE_VIEW_UNITTYPE . "';" .    
//        "const TABLE_NAME_UNITTYPE = '" . TABLE_NAME_UNITTYPE . "';" .
//
//        "const TABLE_VIEW_UNIT = '" . TABLE_VIEW_UNIT . "';" .    
//        "const TABLE_NAME_UNIT = '" . TABLE_NAME_UNIT . "';" .
//
//        "const TABLE_VIEW_UNITLIST = '" .  "#" . TABLE_VIEW_UNITLIST . "';" .    
//        "const TABLE_NAME_UNITLIST = '" . TABLE_NAME_UNITLIST . "';" .
//
//        "const TABLE_VIEW_UNITTREE = '" .  "#" . TABLE_VIEW_UNITTREE . "';" .    
//        "const TABLE_NAME_UNITTREE = '" . TABLE_NAME_UNITTREE . "';" .
//
//        "const TABLE_VIEW_ORGVERSION = '" .  "#" . TABLE_VIEW_ORGVERSION . "';" .    
//        "const TABLE_NAME_ORGVERSION = '" . TABLE_NAME_ORGVERSION . "';" .
//
//        "const TABLE_VIEW_ORG_ROLE_STATUS = '" .  "#" . TABLE_VIEW_ORG_ROLE_STATUS . "';" .    
//        "const TABLE_NAME_ORG_ROLE_STATUS = '" . TABLE_NAME_ORG_ROLE_STATUS . "';" .
//
//    "</script>";
//
//
// 
//
//    

//    echo "<script>" .
//        "const WP_URI = '" . WP_URI . "';" .
//        "const SARON_URI = '" . SARON_URI . "';" .
//        "const SARON_IMAGES_URI = '" . SARON_IMAGES_URI . "';" .
//        "const FullNameOfCongregation = '" . FullNameOfCongregation . "';" .
//        "const SESSION_EXPIRES = '" . SESSION_EXPIRES . "';" .
//    "</script>";
//
////from query.php
//    echo "<script>" .
//        "const RECORD = '" . RECORD . "';" .
//        "const RECORDS = '" . RECORDS . "';" .
//        "const OPTIONS = '" . OPTIONS . "';" .
//        "const SOURCE_LIST = '" . SOURCE_LIST . "';" .
//        "const SOURCE_CREATE = '" . SOURCE_CREATE . "';" .
//        "const SOURCE_EDIT = '" . SOURCE_EDIT . "';" .
//    "</script>";
//
//    
// From this file
    
    //        "const TABLE_VIEW_BAPTIST = '" .  "#" . TABLE_VIEW_BAPTIST . "';" .     
//        "const TABLE_NAME_BAPTIST = '" . TABLE_NAME_BAPTIST . "';" .
//    
//        "const TABLE_VIEW_PEOPLE = '" .  "#" . TABLE_VIEW_PEOPLE . "';" .     
//        "const TABLE_NAME_PEOPLE = '" . TABLE_NAME_PEOPLE . "';" .
//
//        "const TABLE_VIEW_HOMES = '" .  "#" . TABLE_VIEW_HOMES . "';" .    
//        "const TABLE_NAME_HOMES = '" . TABLE_NAME_HOMES . "';" .
//
//        "const TABLE_VIEW_MEMBER = '" .  "#" . TABLE_VIEW_MEMBER . "';" .    
//        "const TABLE_NAME_MEMBER = '" . TABLE_NAME_MEMBER . "';" .
    
    
//// Registry    
//    echo "<script>" .
//
//
//        "const TABLE_VIEW_KEYS = '" .  "#" . TABLE_VIEW_KEYS . "';" .    
//        "const TABLE_NAME_KEYS = '" . TABLE_NAME_KEYS . "';" .
//
//        "const TABLE_VIEW_BIRTHDAY = '" .  "#" . TABLE_VIEW_BIRTHDAY . "';" .    
//        "const TABLE_NAME_BIRTHDAY = '" . TABLE_NAME_BIRTHDAY . "';" .
//
//        "const TABLE_VIEW_TOTAL = '" .  "#" . TABLE_VIEW_TOTAL . "';" .    
//        "const TABLE_NAME_TOTAL = '" . TABLE_NAME_TOTAL . "';" .
//            
//        "const TABLE_VIEW_EFK = '" .  "#" . TABLE_VIEW_EFK . "';" .    
//        "const TABLE_NAME_EFK = '" . TABLE_NAME_EFK . "';" .
//            
//        "const TABLE_VIEW_STATISTICS = '" .  "#" . TABLE_VIEW_STATISTICS . "';" .    
//        "const TABLE_NAME_STATISTICS = '" . TABLE_NAME_STATISTICS . "';" .
//            
//        "const GRAPH_VIEW_STATISTICS = '" .  "#" . GRAPH_VIEW_STATISTICS . "';" .    
//        "const GRAPH_NAME_STATISTICS = '" . GRAPH_NAME_STATISTICS . "';" .
//            
//        "const TABLE_VIEW_STATISTICS_DETAIL = '" .  "#" . TABLE_VIEW_STATISTICS_DETAIL . "';" .    
//        "const TABLE_NAME_STATISTICS_DETAIL = '" . TABLE_NAME_STATISTICS_DETAIL . "';" .
//    "</script>";