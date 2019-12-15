<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';
require_once WP_ROOT . 'wp-includes/user.php';



    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
        $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        $jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        
        $users = Array();
        
        $users = get_users(array('role__in' => array(SARON_ROLE_PREFIX . SARON_ROLE_EDITOR, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER, "wp_otp")));

        $sort_dimension = "display_name";
        $sort_order = "asc";

        if(strlen($jtSorting)>0){
            $pos = strpos($jtSorting, ' ');
            if($pos>0){
                $sort_dimension = substr($jtSorting, 0, $pos);
                $sort_order = substr($jtSorting, $pos + 1, strlen($jtSorting));
            }
        }

        usort($users, function ($a, $b) use($sort_dimension, $sort_order) {
            switch ($sort_dimension){
                case "user_login":
                    if ($a->user_login == $b->user_login) {
                        return 0;
                    }
                    $comp = ($a->user_login < $b->user_login) ? -1: 1;
                break;
                case "display_name":
                    if ($a->display_name == $b->display_name) {
                        return 0;
                    }
                    $comp = ($a->display_name < $b->display_name) ? -1 : 1;
                break;
                case "user_email":
                    if ($a->user_email == $b->user_email) {
                        return 0;
                    }
                    $comp = ($a->user_email < $b->user_email) ? -1 : 1;
                break;
                case "wp_otp":
                    $otp_a = $a->get("wp-otp");
                    $otp_b = $b->get("wp-otp");   
                    if ($otp_a["enabled"] == $otp_b["enabled"]) {
                        return 0;
                    }
                    $comp = ($otp_a["enabled"] < $otp_b["enabled"]) ? -1 : 1;
                break;
                case "saron_reader":
                    $viewer_a = hasPrivilege($a->roles, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER);
                    $viewer_b = hasPrivilege($b->roles, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER);
                    if ($viewer_a == $viewer_b) {
                        return 0;
                    }
                    $comp = ($viewer_a < $viewer_b) ? -1 : 1;
                break;
                case "saron_editor":
                    $editor_a = hasPrivilege($a->roles, SARON_ROLE_PREFIX . SARON_ROLE_EDITOR);
                    $editor_b = hasPrivilege($b->roles, SARON_ROLE_PREFIX . SARON_ROLE_EDITOR);
                    if ($editor_a == $editor_b) {
                        return 0;
                    }
                    $comp = ($editor_a < $editor_b) ? -1 : 1;
                break;
            }
            if($sort_order === "ASC"){
                return $comp;
            }
            else{
                return -$comp; 
            }
        });

        $endIndex=0;
        if(count($users) > $jtStartIndex + $jtPageSize){
            $endIndex = $jtStartIndex + $jtPageSize;
        }
        else{
            $endIndex = count($users);
        }
        $result = '{"Result":"OK","Records":[';
        for($i = $jtStartIndex; $i<$endIndex; $i++){
            $viewer = hasPrivilege($users[$i]->roles, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER);
            $editor = hasPrivilege($users[$i]->roles, SARON_ROLE_PREFIX . SARON_ROLE_EDITOR);
            $result.= '{"id":' . $users[$i]->ID;
            $result.= ',"display_name":"' . $users[$i]->display_name; 
            $result.= '","user_login":"' . $users[$i]->user_login; 
            $result.= '","user_email":"' . $users[$i]->user_email; 
            $otp = $users[$i]->get("wp-otp");
            $result.= '","wp_otp":"' . $otp["enabled"]; 
            $result.= '","saron_reader":' . $viewer; 
            $result.= ',"saron_editor":' . $editor . '}';
            if($i<$endIndex-1){
                $result.=",";
            }
        }
        $result.='],"TotalRecordCount":' . count($users);
        if($saronUser->isEditor()){
            $result.=',"user_role":"' . SARON_ROLE_EDITOR . '"';
        }
        else{
            $result.=',"user_role":"' . SARON_ROLE_VIEWER . '"';
        }
        $result.='}';

        echo $result;
    }

    function hasPrivilege($user_roles, $privelege){
        for($i=0; $i<count($user_roles); $i++){
            if($user_roles[$i] === $privelege){
                return 1;
            }
        }
        return 0;
    }
    