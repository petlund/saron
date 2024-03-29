<?php

require_once "config.php";
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';


class SaronUsers extends SuperEntity{
    private $users = Array();
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        try{
            $this->users = get_users(array('role__in' => array(SARON_ROLE_PREFIX . SARON_ROLE_EDITOR, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER, SARON_ROLE_PREFIX . SARON_ROLE_ORG, "wp_otp")));
        }
        catch(Exception $e){
            $this->throwUiMessage("SaronUsers",  "__construct", $e);
        }
        
    }
        
    function sort($sort_dimension = "display_name", $sort_order = "asc"){
        if(strlen($this->jtSorting)>0){
            $pos = strpos($this->jtSorting, ' ');
            if($pos>0){
                $sort_dimension = substr($this->jtSorting, 0, $pos);
                $sort_order = substr($this->jtSorting, $pos + 1, strlen($this->jtSorting));
            }
        }

        usort($this->users, function ($a, $b) use($sort_dimension, $sort_order) {
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
                    $viewer_a = $this->hasPrivilege($a->roles, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER);
                    $viewer_b = $this->hasPrivilege($b->roles, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER);
                    if ($viewer_a == $viewer_b) {
                        return 0;
                    }
                    $comp = ($viewer_a < $viewer_b) ? -1 : 1;
                break;
                case "saron_editor":
                    $edit_a = $this->hasPrivilege($a->roles, SARON_ROLE_PREFIX . SARON_ROLE_EDITOR);
                    $edit_b = $this->hasPrivilege($b->roles, SARON_ROLE_PREFIX . SARON_ROLE_EDITOR);
                    if ($edit_a == $edit_b) {
                        return 0;
                    }
                    $comp = ($edit_a < $edit_b) ? -1 : 1;
                break;
                case "saron_org":
                    $org_a = $this->hasPrivilege($a->roles, SARON_ROLE_PREFIX . SARON_ROLE_ORG);
                    $org_b = $this->hasPrivilege($b->roles, SARON_ROLE_PREFIX . SARON_ROLE_ORG);
                    if ($org_a == $org_b) {
                        return 0;
                    }
                    $comp = ($org_a < $org_b) ? -1 : 1;
                break;
                default:
                    $comp = 1;
                break;
            }
            if($sort_order === "ASC"){
                return $comp;
            }
            else{
                return -$comp; 
            }
        });
    
    }
    
    function createResultRecords($sort_dimension, $sort_order){
        $this->sort($sort_dimension, $sort_order);

        $endIndex=0;
        if(count($this->users) > $this->jtStartIndex + $this->jtPageSize){
            $endIndex = $this->jtStartIndex + $this->jtPageSize;
        }
        else{
            $endIndex = count($this->users);
        }

        $result = '{"Result":"OK","Records":[';
        for($i = $this->jtStartIndex; $i < $endIndex; $i++){
            $viewer = $this->hasPrivilege($this->users[$i]->roles, SARON_ROLE_PREFIX . SARON_ROLE_VIEWER);
            $org = $this->hasPrivilege($this->users[$i]->roles, SARON_ROLE_PREFIX . SARON_ROLE_ORG);
            $edit = $this->hasPrivilege($this->users[$i]->roles, SARON_ROLE_PREFIX . SARON_ROLE_EDITOR);

            
            if($this->users[$i] !== null){
                $ID=$i;
                if($this->users[$i]->ID !== null){
                    $ID = $this->users[$i]->ID;
                }

                $display_name = "display_name";
                if($this->users[$i]->display_name !== null){
                    $display_name = $this->users[$i]->display_name;
                }

                $user_login = "user_login";
                if($this->users[$i]->user_login !== null){
                    $user_login = $this->users[$i]->user_login;
                }

                $user_email ="user_email";
                if($this->users[$i]->user_email !== null){
                    $user_email = $this->users[$i]->user_email;
                }


                $result.= '{"Id":' . $ID;
    //            $result.= ',"OpenChildTable":' . $this->openChildTable ;
                $result.= ',"display_name":"' . $display_name; 
                $result.= '","user_login":"' . $user_login; 
                $result.= '","user_email":"' . $user_email; 

                $otp = $this->users[$i]->get("wp-otp");       
                
                if(is_array($otp)){
                    if(array_key_exists("enabled",$otp)){
                        $enabled = $otp["enabled"];
                        if($enabled){
                            $result.= '","wp_otp":"1"';             
                        }
                        else{
                            $result.= '","wp_otp":"0"';
                        }
                    }
                    else{
                        $result.= '","wp_otp":"0"';
                    }
                }
                else{
                    $result.= '","wp_otp":"0"';
                }
                
                $result.= ',"saron_reader":' . $viewer; 
                $result.= ',"saron_org":' . $org; 
                $result.= ',"saron_editor":' . $edit . '}';
                if($i < $endIndex-1){
                    $result.= ",";
                }
            }
        }
        $result.='],"TotalRecordCount":' . count($this->users);
        $result.=',"user_role":"' . $this->saronUser->getRole() . '"';
        $result.='}';

        return $result;
    }
    



    function createResultOptions($sort_dimension, $sort_order){
        $this->throwUiMessage("SaronUsers", "createResultOptions", "manual abort");
        $usrs = Array(); 
        $this->sort($sort_dimension, $sort_order);

        $cntActiveUsers = count($this->users);
        for($i = 0; $i < $cntActiveUsers; $i++){
            $usr = Array();
            $usr["ID"] = $this->users[$i]->ID;
            $usr["display_name"] = $this->users[$i]->display_name;
            $usrs[] = $usr;
        }
        
        switch ($this->selection){
        case "People":
            $table = "People";
            break;
        case "role":
            $table = "Org_Role";
            break;
        case "pos":
            $table = "Org_Pos";
            break;
        case "unit":
            $table = "Org_UnitType";
            break;
        case "tree":
            $table = "Org_Tree";
            break;
        case "status":
            $table = "Org_PosStatus";
            break;
        case "memberstate":
            $table = "MemberState";
            break;
        default:
            $table = "People";
        }

        
        try{
            $db = new db();            
            $sql = "select UpdaterName as ID, 'Användarnamn saknas!' as display_name from " . $table . $this->getUpdaterNameSet() . " GROUP BY UpdaterName";
            $listResult = $db->sqlQuery($sql);
            foreach($listResult as $aRow){
                $usrs[] = $aRow;
            }    
        } 
        catch (Exception $e) {
            $this->throwUiMessage("SaronUsers", "Exception in createResultOptions: " + $e);
        }
        finally {
            $cntOldUsers = count($usrs);
            $result = '{"Result":"OK","Options":[';
            for($i = 0; $i < $cntOldUsers; $i++){
                $result.= '{"Value":"' . $usrs[$i]["ID"] . '",';
                $result.= '"DisplayText":"' . $usrs[$i]["display_name"] . '"}'; 
                if($i<$cntOldUsers-1){
                    $result.=',';
                }
            }
            $result.=']}';
            return $result;            
        }
    }

    
    private function getUpdaterNameSet(){
        $cntActiveUsers = count($this->users);
        $result = " where UpdaterName NOT in (";
        for($i = 0; $i < $cntActiveUsers; $i++){
            $result.= $this->users[$i]->ID;
            if($i<$cntActiveUsers-1){
                $result.= ",";
            }
        }        
        $result.= ") "; 
        return $result;
    }



    function getSaronUsers($resultFormat = RECORDS){
        If($resultFormat === OPTIONS){
            return $this->createResultOptions("display_name", "asc");
        }
        else{
            return $this->createResultRecords("display_name", "asc");
        }
    }


    function hasPrivilege($user_roles, $privelege){
        for($i=0; $i<count($user_roles); $i++){
            if($user_roles[$i] === $privelege){
                return 1;
            }
        }
        return 0;
    }
}
