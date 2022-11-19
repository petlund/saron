<?php
require_once "config.php";
require_once SARON_ROOT . "app/util/GlobalConstants_php.php";

// From config.php
// System
$saron = (object)array(); 
// $saron->success = false; 
$saron->uri = (object)array();
$saron->root = (object)array();
$saron->uri->wp = WP_URI;
$saron->uri->saron = SARON_URI;
$saron->root->saron = "/" . SARON_URI;
$saron->uri->images = SARON_IMAGES_URI;
$saron->root->images = "/" . SARON_URI . SARON_IMAGES_URI;
$saron->uri->webapi = SARON_WEBAPI_URI;
$saron->root->webapi = "/" . SARON_URI . SARON_WEBAPI_URI;
$saron->uri->pdf = SARON_PDF_URI;
$saron->root->pdf = "/" . SARON_URI . SARON_PDF_URI;
$saron->root->reports = "/" . SARON_URI . SARON_REPORTS_URI;


$saron->userrole = (object)array();
$saron->userrole->viewer = SARON_ROLE_VIEWER;
$saron->userrole->editor = SARON_ROLE_EDITOR;
$saron->userrole->org_editor = SARON_ROLE_ORG;

$saron->memberstate = (object)array();
$saron->memberstate->aononymized = PEOPLE_STATE_ANONYMiZED;
$saron->memberstate->dead = PEOPLE_STATE_DEAD;
$saron->memberstate->membership = PEOPLE_STATE_MEMBERSHIP;
$saron->memberstate->membership_ended = PEOPLE_STATE_MEMBERSHIP_ENDED;
$saron->memberstate->only_baptist = PEOPLE_STATE_ONLY_BAPTIST;
$saron->memberstate->registrated = PEOPLE_STATE_REGISTRATED;

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
$saron->table->news->nameId = '#NEWS';
$saron->table->news->name = 'NEWS';

$saron->table->baptist = (object)array();
$saron->table->baptist->nameId = "#" . TABLE_NAME_BAPTIST ;
$saron->table->baptist->name = TABLE_NAME_BAPTIST ;

$saron->table->people = (object)array();
$saron->table->people->nameId = "#" . TABLE_NAME_PEOPLE;
$saron->table->people->name = TABLE_NAME_PEOPLE;

$saron->table->member = (object)array();
$saron->table->member->nameId = "#" . TABLE_NAME_MEMBER ;
$saron->table->member->name = TABLE_NAME_MEMBER ;

$saron->table->homes = (object)array();
$saron->table->homes->nameId = "#" . TABLE_NAME_HOMES ;
$saron->table->homes->name = TABLE_NAME_HOMES ;

$saron->table->keys = (object)array();
$saron->table->keys->nameId = "#" . TABLE_NAME_KEYS ;
$saron->table->keys->name = TABLE_NAME_KEYS ;

$saron->table->birthday = (object)array();
$saron->table->birthday->nameId = "#" . TABLE_NAME_BIRTHDAY ;
$saron->table->birthday->name = TABLE_NAME_BIRTHDAY ;

$saron->table->member_state = (object)array();
$saron->table->member_state->nameId = "#" . TABLE_NAME_MEMBER_STATE ;
$saron->table->member_state->name = TABLE_NAME_MEMBER_STATE ;

$saron->table->member_state_report = (object)array();
$saron->table->member_state_report->nameId = "#" . TABLE_NAME_MEMBER_STATE_REPORT ;
$saron->table->member_state_report->name = TABLE_NAME_MEMBER_STATE_REPORT ;

$saron->table->total = (object)array();
$saron->table->total->nameId = "#" . TABLE_NAME_TOTAL ;
$saron->table->total->name = TABLE_NAME_TOTAL ;

$saron->table->efk = (object)array();
$saron->table->efk->nameId = "#" . TABLE_NAME_EFK ;
$saron->table->efk->name = TABLE_NAME_EFK ;

$saron->table->statistics = (object)array();
$saron->table->statistics->nameId = "#" . TABLE_NAME_STATISTICS ;
$saron->table->statistics->name = TABLE_NAME_STATISTICS ;

$saron->table->statistics_detail = (object)array();
$saron->table->statistics_detail->nameId = "#" . TABLE_NAME_STATISTICS_DETAIL ;
$saron->table->statistics_detail->name = TABLE_NAME_STATISTICS_DETAIL ;

$saron->graph = (object)array();
$saron->graph->timeseries = (object)array();
$saron->graph->timeseries->nameId = "#" . GRAPH_NAME_TIME_SERIES ;
$saron->graph->timeseries->name = GRAPH_NAME_TIME_SERIES ;
$saron->graph->histogram = (object)array();
$saron->graph->histogram->nameId = "#" . GRAPH_NAME_HISTOGRAM ;
$saron->graph->histogram->name = GRAPH_NAME_HISTOGRAM ;

$saron->table->role = (object)array();
$saron->table->role->nameId = "#" . TABLE_NAME_ROLE ;
$saron->table->role->name = TABLE_NAME_ROLE ;

$saron->table->engagement = (object)array();
$saron->table->engagement->nameId = "#" . TABLE_NAME_ENGAGEMENT ;
$saron->table->engagement->name = TABLE_NAME_ENGAGEMENT;
$saron->table->engagements = (object)array();
$saron->table->engagements->name = TABLE_NAME_ENGAGEMENTS ;

$saron->table->pos = (object)array();
$saron->table->pos->nameId = "#" . TABLE_NAME_POS ;
$saron->table->pos->name = TABLE_NAME_POS ;

$saron->table->posinstances = (object)array();
$saron->table->posinstances->nameId = "#" . TABLE_NAME_POS_INSTANCES ;
$saron->table->posinstances->name = TABLE_NAME_POS_INSTANCES ;

$saron->table->unittype = (object)array();
$saron->table->unittype->nameId = "#" . TABLE_NAME_UNITTYPE ;
$saron->table->unittype->name = TABLE_NAME_UNITTYPE ;

$saron->table->role_unittype = (object)array();
$saron->table->role_unittype->nameId = "#" . TABLE_NAME_ROLE_UNITTYPE ;
$saron->table->role_unittype->name = TABLE_NAME_ROLE_UNITTYPE ;

$saron->table->unit = (object)array();
$saron->table->unit->nameId = "#" . TABLE_NAME_UNIT ;
$saron->table->unit->name = TABLE_NAME_UNIT ;

$saron->table->unitlist = (object)array();
$saron->table->unitlist->nameId = "#" . TABLE_NAME_UNITLIST ;
$saron->table->unitlist->name = TABLE_NAME_UNITLIST ;

$saron->table->unittree = (object)array();
$saron->table->unittree->nameId = "#" . TABLE_NAME_UNITTREE ;
$saron->table->unittree->name = TABLE_NAME_UNITTREE ;

$saron->table->orgversion = (object)array();
$saron->table->orgversion->nameId = "#" . TABLE_NAME_ORGVERSION ;
$saron->table->orgversion->name = TABLE_NAME_ORGVERSION ;

$saron->table->org_role_status = (object)array();
$saron->table->org_role_status->nameId = "#" . TABLE_NAME_ORG_ROLE_STATUS ;
$saron->table->org_role_status->name = TABLE_NAME_ORG_ROLE_STATUS ;

$saron->table->users = (object)array();
$saron->table->users->nameId = "#" . TABLE_NAME_USERS ;
$saron->table->users->name = TABLE_NAME_USERS ;

$saron->table->changelog =  (object)array();
$saron->table->changelog->name = TABLE_NAME_CHANGE;
$saron->table->changelog->nameId = "#" . TABLE_NAME_CHANGE;

$saron->list = (object)array();
$saron->list->mobile_instead_of_email =  (object)array();
$saron->list->mobile_instead_of_email->nameId = "#" . LIST_MOBILE_INSTEAD_OF_EMAIL;
$saron->list->mobile_instead_of_email->name = LIST_MOBILE_INSTEAD_OF_EMAIL;

$saron->list->email =  (object)array();
$saron->list->email->name = LIST_EMAIL;
$saron->list->email->nameId = "#" . LIST_EMAIL;

$saron->list->email_member =  (object)array();
$saron->list->email_member->name = LIST_EMAIL_MEMBER;
$saron->list->email_member->nameId = "#" . LIST_EMAIL_MEMBER;

$saron->list->email_friendship =  (object)array();
$saron->list->email_friendship->name = LIST_EMAIL_FRIENDSHIP;
$saron->list->email_friendship->nameId = "#" . LIST_EMAIL_FRIENDSHIP;

$saron->list->email_ending_friendship =  (object)array();
$saron->list->email_ending_friendship->name = LIST_EMAIL_ENDING_FRIENDSHIP;
$saron->list->email_ending_friendship->nameId = "#" . LIST_EMAIL_ENDING_FRIENDSHIP;

$saron->list->mobile =  (object)array();
$saron->list->mobile->name = LIST_MOBILE;
$saron->list->mobile->nameId = "#" . LIST_MOBILE;

$saron->list->mobile_member =  (object)array();
$saron->list->mobile_member->name = LIST_MOBILE_MEMBER;
$saron->list->mobile_member->nameId = "#" . LIST_MOBILE_MEMBER;

$saron->list->mobile_friendship =  (object)array();
$saron->list->mobile_friendship->name = LIST_MOBILE_FRIENDSHIP;
$saron->list->mobile_friendship->nameId = "#" . LIST_MOBILE_FRIENDSHIP;

$saron->list->mobile_ending_friendship =  (object)array();
$saron->list->mobile_ending_friendship->name = LIST_MOBILE_ENDING_FRIENDSHIP;
$saron->list->mobile_ending_friendship->nameId = "#" . LIST_MOBILE_ENDING_FRIENDSHIP;

$saronJSON = json_encode($saron);
