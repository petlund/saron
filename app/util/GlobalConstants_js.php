<?php
require_once "config.php";
require_once SARON_ROOT . "app/util/GlobalConstants_php.php";

// From config.php
// System
    echo "<script>const WP_URI = '" . WP_URI . "';</script>";
    echo "<script>const SARON_URI = '" . SARON_URI . "';</script>";
    echo "<script>const SARON_IMAGES_URI = '" . SARON_IMAGES_URI . "';</script>";
    echo "<script>const FullNameOfCongregation = '" . FullNameOfCongregation . "';</script>";
    echo "<script>const SESSION_EXPIRES = '" . SESSION_EXPIRES . "';</script>";

//from query.php
    echo "<script>const RECORD = '" . RECORD . "';</script>";
    echo "<script>const RECORDS = '" . RECORDS . "';</script>";
    echo "<script>const OPTIONS = '" . OPTIONS . "';</script>";
    echo "<script>const SOURCE_LIST = '" . SOURCE_LIST . "';</script>";
    echo "<script>const SOURCE_CREATE = '" . SOURCE_CREATE . "';</script>";
    echo "<script>const SOURCE_EDIT = '" . SOURCE_EDIT . "';</script>";

    
// From this file
// People    
    echo "<script>const TABLE_VIEW_PEOPLE = '" .  "#" . TABLE_VIEW_PEOPLE . "';</script>";    
    echo "<script>const TABLE_NAME_PEOPLE = '" . TABLE_NAME_PEOPLE . "';</script>";
    
//Organisation
    echo "<script>const TABLE_VIEW_CHILD_SUFFIX = '" . TABLE_VIEW_CHILD_SUFFIX . "';</script>";

    echo "<script>const TABLE_VIEW_ROLE = '" .  "#" . TABLE_VIEW_ROLE . "';</script>";
    echo "<script>const TABLE_NAME_ROLE = '" . TABLE_NAME_ROLE . "';</script>";

    echo "<script>const TABLE_VIEW_ENGAGEMENT = '" .  "#" . TABLE_VIEW_ENGAGEMENT . "';</script>";
    echo "<script>const TABLE_NAME_ENGAGEMENT = '" . TABLE_NAME_ENGAGEMENT . "';</script>";

    echo "<script>const TABLE_VIEW_POS = '" .  "#" . TABLE_VIEW_POS . "';</script>";
    echo "<script>const TABLE_NAME_POS = '" . TABLE_NAME_POS . "';</script>";

    echo "<script>const TABLE_VIEW_ROLE_UNITTYPE = '" . "#" . TABLE_VIEW_ROLE_UNITTYPE . "';</script>";    
    echo "<script>const TABLE_NAME_ROLE_UNITTYPE = '" . TABLE_NAME_ROLE_UNITTYPE . "';</script>";

    echo "<script>const TABLE_VIEW_UNITTYPE = '" .  "#" . TABLE_VIEW_UNITTYPE . "';</script>";    
    echo "<script>const TABLE_NAME_UNITTYPE = '" . TABLE_NAME_UNITTYPE . "';</script>";

//    echo "<script>const TABLE_VIEW_UNIT = '" . TABLE_VIEW_UNIT . "';</script>";    
    echo "<script>const TABLE_NAME_UNIT = '" . TABLE_NAME_UNIT . "';</script>";

    echo "<script>const TABLE_VIEW_UNITLIST = '" .  "#" . TABLE_VIEW_UNITLIST . "';</script>";    
    echo "<script>const TABLE_NAME_UNITLIST = '" . TABLE_NAME_UNITLIST . "';</script>";

    echo "<script>const TABLE_VIEW_UNITTREE = '" .  "#" . TABLE_VIEW_UNITTREE . "';</script>";    
    echo "<script>const TABLE_NAME_UNITTREE = '" . TABLE_NAME_UNITTREE . "';</script>";

    echo "<script>const TABLE_VIEW_ORGVERSION = '" .  "#" . TABLE_VIEW_ORGVERSION . "';</script>";    
    echo "<script>const TABLE_NAME_ORGVERSION = '" . TABLE_NAME_ORGVERSION . "';</script>";

    echo "<script>const TABLE_VIEW_ORG_ROLE_STATUS = '" .  "#" . TABLE_VIEW_ORG_ROLE_STATUS . "';</script>";    
    echo "<script>const TABLE_NAME_ORG_ROLE_STATUS = '" . TABLE_NAME_ORG_ROLE_STATUS . "';</script>";


 

    