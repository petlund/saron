<?php
require_once "config.php";
require_once SARON_ROOT . "app/util/GlobalConstants_php.php";

// From config.php
// System
    echo "<script>" .
        "const WP_URI = '" . WP_URI . "';" .
        "const SARON_URI = '" . SARON_URI . "';" .
        "const SARON_IMAGES_URI = '" . SARON_IMAGES_URI . "';" .
        "const FullNameOfCongregation = '" . FullNameOfCongregation . "';" .
        "const SESSION_EXPIRES = '" . SESSION_EXPIRES . "';" .
    "</script>";

//from query.php
    echo "<script>" .
        "const RECORD = '" . RECORD . "';" .
        "const RECORDS = '" . RECORDS . "';" .
        "const OPTIONS = '" . OPTIONS . "';" .
        "const SOURCE_LIST = '" . SOURCE_LIST . "';" .
        "const SOURCE_CREATE = '" . SOURCE_CREATE . "';" .
        "const SOURCE_EDIT = '" . SOURCE_EDIT . "';" .
    "</script>";

    
// From this file
// Registry    
    echo "<script>" .
        "const TABLE_VIEW_BAPTIST = '" .  "#" . TABLE_VIEW_BAPTIST . "';" .     
        "const TABLE_NAME_BAPTIST = '" . TABLE_NAME_BAPTIST . "';" .
    
        "const TABLE_VIEW_PEOPLE = '" .  "#" . TABLE_VIEW_PEOPLE . "';" .     
        "const TABLE_NAME_PEOPLE = '" . TABLE_NAME_PEOPLE . "';" .

        "const TABLE_VIEW_HOMES = '" .  "#" . TABLE_VIEW_HOMES . "';" .    
        "const TABLE_NAME_HOMES = '" . TABLE_NAME_HOMES . "';" .

        "const TABLE_VIEW_MEMBER = '" .  "#" . TABLE_VIEW_MEMBER . "';" .    
        "const TABLE_NAME_MEMBER = '" . TABLE_NAME_MEMBER . "';" .

        "const TABLE_VIEW_KEYS = '" .  "#" . TABLE_VIEW_KEYS . "';" .    
        "const TABLE_NAME_KEYS = '" . TABLE_NAME_KEYS . "';" .

        "const TABLE_VIEW_BIRTHDAY = '" .  "#" . TABLE_VIEW_BIRTHDAY . "';" .    
        "const TABLE_NAME_BIRTHDAY = '" . TABLE_NAME_BIRTHDAY . "';" .

        "const TABLE_VIEW_TOTAL = '" .  "#" . TABLE_VIEW_TOTAL . "';" .    
        "const TABLE_NAME_TOTAL = '" . TABLE_NAME_TOTAL . "';" .
    "</script>";

    //Organisation
    echo "<script>" .
        "const TABLE_VIEW_CHILD_SUFFIX = '" . TABLE_VIEW_CHILD_SUFFIX . "';" .

        "const TABLE_VIEW_ROLE = '" .  "#" . TABLE_VIEW_ROLE . "';" .
        "const TABLE_NAME_ROLE = '" . TABLE_NAME_ROLE . "';" .

        "const TABLE_VIEW_ENGAGEMENT = '" .  "#" . TABLE_VIEW_ENGAGEMENT . "';" .
        "const TABLE_NAME_ENGAGEMENT = '" . TABLE_NAME_ENGAGEMENT . "';" .

        "const TABLE_VIEW_POS = '" .  "#" . TABLE_VIEW_POS . "';" .
        "const TABLE_NAME_POS = '" . TABLE_NAME_POS . "';" .

        "const TABLE_VIEW_ROLE_UNITTYPE = '" . "#" . TABLE_VIEW_ROLE_UNITTYPE . "';" .    
        "const TABLE_NAME_ROLE_UNITTYPE = '" . TABLE_NAME_ROLE_UNITTYPE . "';" .

        "const TABLE_VIEW_UNITTYPE = '" .  "#" . TABLE_VIEW_UNITTYPE . "';" .    
        "const TABLE_NAME_UNITTYPE = '" . TABLE_NAME_UNITTYPE . "';" .

        "const TABLE_VIEW_UNIT = '" . TABLE_VIEW_UNIT . "';" .    
        "const TABLE_NAME_UNIT = '" . TABLE_NAME_UNIT . "';" .

        "const TABLE_VIEW_UNITLIST = '" .  "#" . TABLE_VIEW_UNITLIST . "';" .    
        "const TABLE_NAME_UNITLIST = '" . TABLE_NAME_UNITLIST . "';" .

        "const TABLE_VIEW_UNITTREE = '" .  "#" . TABLE_VIEW_UNITTREE . "';" .    
        "const TABLE_NAME_UNITTREE = '" . TABLE_NAME_UNITTREE . "';" .

        "const TABLE_VIEW_ORGVERSION = '" .  "#" . TABLE_VIEW_ORGVERSION . "';" .    
        "const TABLE_NAME_ORGVERSION = '" . TABLE_NAME_ORGVERSION . "';" .

        "const TABLE_VIEW_ORG_ROLE_STATUS = '" .  "#" . TABLE_VIEW_ORG_ROLE_STATUS . "';" .    
        "const TABLE_NAME_ORG_ROLE_STATUS = '" . TABLE_NAME_ORG_ROLE_STATUS . "';" .

    "</script>";


 

    