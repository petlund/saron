<?php
require_once "config.php";
require_once SARON_ROOT . "app/util/GlobalConstants_php.php";

// From config.php
// System
$saron = (object)array(); 
// $saron->success = false; 
$saron->uri = (object)array();
$saron->uri->wp = WP_URI;
$saron->uri->saron = SARON_URI;
$saron->uri->images = SARON_IMAGES_URI;

$saron->userrole = (object)array();
$saron->userrole->viewer = SARON_ROLE_VIEWER;
$saron->userrole->editor = SARON_ROLE_EDITOR;
$saron->userrole->org_editor = SARON_ROLE_ORG;

$saron->formtype = (object)array();
$saron->formtype->edit = 'edit';
$saron->formtype->create = 'create';

$saron->name = (object)array();
$saron->name->full_name = FullNameOfCongregation;

$saron->session = (object)array();
$saron->session->expires_time = SESSION_EXPIRES;

$saron->responsetype = (object)array();
$saron->responsetype->record = RECORD;
$saron->responsetype->records = RECORDS;
$saron->responsetype->options = OPTIONS;

$saron->source = (object)array();
$saron->source->list = SOURCE_LIST;
$saron->source->edit = SOURCE_EDIT;
$saron->source->create = SOURCE_CREATE;

$saron->table = (object)array();
$saron->table->news = (object)array();
$saron->table->news->viewid = '#NEWS';
$saron->table->news->name = 'NEWS';

$saron->table->baptist = (object)array();
$saron->table->baptist->viewid = "#" . TABLE_VIEW_BAPTIST ;
$saron->table->baptist->name = TABLE_NAME_BAPTIST ;

$saron->table->people = (object)array();
$saron->table->people->viewid = "#" . TABLE_VIEW_PEOPLE;
$saron->table->people->name = TABLE_NAME_PEOPLE;

$saron->table->member = (object)array();
$saron->table->member->viewid = "#" . TABLE_VIEW_MEMBER ;
$saron->table->member->name = TABLE_NAME_MEMBER ;

$saron->table->homes = (object)array();
$saron->table->homes->viewid = "#" . TABLE_VIEW_HOMES ;
$saron->table->homes->name = TABLE_NAME_HOMES ;

$saron->table->keys = (object)array();
$saron->table->keys->viewid = "#" . TABLE_VIEW_KEYS ;
$saron->table->keys->name = TABLE_NAME_KEYS ;

$saron->table->birthday = (object)array();
$saron->table->birthday->viewid = "#" . TABLE_VIEW_BIRTHDAY ;
$saron->table->birthday->name = TABLE_NAME_BIRTHDAY ;

$saron->table->total = (object)array();
$saron->table->total->viewid = "#" . TABLE_VIEW_TOTAL ;
$saron->table->total->name = TABLE_NAME_TOTAL ;

$saron->table->efk = (object)array();
$saron->table->efk->viewid = "#" . TABLE_VIEW_EFK ;
$saron->table->efk->name = TABLE_NAME_EFK ;

$saron->table->statistics = (object)array();
$saron->table->statistics->viewid = "#" . TABLE_VIEW_STATISTICS ;
$saron->table->statistics->name = TABLE_NAME_STATISTICS ;

$saron->table->statistics_detail = (object)array();
$saron->table->statistics_detail->viewid = "#" . TABLE_VIEW_STATISTICS_DETAIL ;
$saron->table->statistics_detail->name = TABLE_NAME_STATISTICS_DETAIL ;

$saron->graph = (object)array();
$saron->graph->statistics = (object)array();
$saron->graph->statistics->viewid = "#" . GRAPH_VIEW_STATISTICS ;
$saron->graph->statistics->name = GRAPH_NAME_STATISTICS ;

$saron->table->role = (object)array();
$saron->table->role->viewid = "#" . TABLE_VIEW_ROLE ;
$saron->table->role->name = TABLE_NAME_ROLE ;

$saron->table->engagement = (object)array();
$saron->table->engagement->viewid = "#" . TABLE_VIEW_ENGAGEMENT ;
$saron->table->engagement->name = TABLE_NAME_ENGAGEMENT;
$saron->table->engagements = (object)array();
$saron->table->engagements->name = TABLE_NAME_ENGAGEMENTS ;

$saron->table->pos = (object)array();
$saron->table->pos->viewid = "#" . TABLE_VIEW_POS ;
$saron->table->pos->name = TABLE_NAME_POS ;

$saron->table->unittype = (object)array();
$saron->table->unittype->viewid = "#" . TABLE_VIEW_UNITTYPE ;
$saron->table->unittype->name = TABLE_NAME_UNITTYPE ;

$saron->table->role_unittype = (object)array();
$saron->table->role_unittype->viewid = "#" . TABLE_VIEW_ROLE_UNITTYPE ;
$saron->table->role_unittype->name = TABLE_NAME_ROLE_UNITTYPE ;

$saron->table->unit = (object)array();
$saron->table->unit->viewid = "#" . TABLE_VIEW_UNIT ;
$saron->table->unit->name = TABLE_NAME_UNIT ;

$saron->table->unitlist = (object)array();
$saron->table->unitlist->viewid = "#" . TABLE_VIEW_UNITLIST ;
$saron->table->unitlist->name = TABLE_NAME_UNITLIST ;

$saron->table->unittree = (object)array();
$saron->table->unittree->viewid = "#" . TABLE_VIEW_UNITTREE ;
$saron->table->unittree->name = TABLE_NAME_UNITTREE ;

$saron->table->orgversion = (object)array();
$saron->table->orgversion->viewid = "#" . TABLE_VIEW_ORGVERSION ;
$saron->table->orgversion->name = TABLE_NAME_ORGVERSION ;

$saron->table->org_role_status = (object)array();
$saron->table->org_role_status->viewid = "#" . TABLE_VIEW_ORG_ROLE_STATUS ;
$saron->table->org_role_status->name = TABLE_NAME_ORG_ROLE_STATUS ;

$saron->table->users = (object)array();
$saron->table->users->viewid = "#" . TABLE_VIEW_USERS ;
$saron->table->users->name = TABLE_NAME_USERS ;

$saron->list = (object)array();
$saron->list->mobile_instead_of_email =  (object)array();
$saron->list->mobile_instead_of_email->viewid = MOBILE_INSTEAD_OF_EMAIL;
$saron->list->email =  (object)array();
$saron->list->email->viewid = EMAIL_LIST;

$saronJSON = json_encode($saron);
