<?php
require_once "config.php";
require_once SARON_ROOT . "app/access/SaronCookie.php";
require_once SARON_ROOT . "app/access/Ticket.php";
require_once SARON_ROOT . "app/database/db.php";

    define( 'WP_USE_THEMES', false );

    function authenticate() { // call only from login.php 
        require_once WP_ROOT . "wp-load.php";

        /*** AUTHENTICATE WP LOGIN ATTEMPT ***/
	$wpUser = wp_signon();
        
	if ( is_wp_error( $wpUser ) ) {
            return false;
	} 
        
        /*** INITIATING PHP SESSION ***/
        if ( ! session_id() ) {
                session_start();
        }
        
        try{
            if(isSaronUser($wpUser)){
                createPersistentSaronSessionUser($wpUser);
            }
            else{
                logout();
                return false;            
            }


            header( 'location: /' . SARON_URI);
            return true;
        }
        catch(Exception $ex){
            return false;
        }
    }
    

    
    function logout(){
        require_once WP_ROOT . "wp-load.php";
        //$ticket = getTicketFromCookie();
        removeSaronCookie();
        try{
            $db = new db();
            $wpUser = wp_get_current_user();
            deletePersistentSaron($db, $wpUser->ID);
        } 
        catch (Exception $ex) {
            ;
        }
        finally{
            wp_logout();
        }
    }
    
    
    
    function deletePersistentSaron($db, $id){
        $db->delete("DELETE FROM SaronUser WHERE WP_ID = " . $id);
    }
    
    
    
    function isSaronUser($wpUser){
        if(isOtpEnabled($wpUser) || isDevEnvironment($wpUser)){
            if(getRole($wpUser) !== null){
                return true;
            }
        }
        return false;
    }
    
 
    
    function createPersistentSaronSessionUser($wpUser){
        $editor=0; 
        if(isEditor($wpUser)){
            $editor = 1;
        }
        
        $org_editor = 0;
        if(isOrgEditor($wpUser)){
            $org_editor=1;
        }
        
        $userDisplayName = $wpUser->display_name;
        $wp_id = $wpUser->ID;

        
        $ticket = insertSaronSessionUser($wp_id, $editor, $org_editor, $userDisplayName);
        setSaronCookie($ticket);
    }
    
    
    
    function insertSaronSessionUser($wp_id, $editor, $org_editor, $userDisplayName ){
        $db = new db();        

        deletePersistentSaron($db, $wp_id);   
        
        $sql = "INSERT INTO SaronUser (AccessTicket, Editor, Org_Editor, WP_ID, UserDisplayName) values (";
        $sql.= getAccessTicket() . ", "; 
        $sql.= $editor . ", ";
        $sql.= $org_editor . ", ";
        $sql.= $wp_id . ", '";
        $sql.= $userDisplayName . "') ";
        echo $sql;
        try{
            $lastId = $db->insert($sql, "SaronUser", "Id");
            $result = $db->sqlQuery("Select AccessTicket from SaronUser where Id = " . $lastId);
    
            $ticket = "";
            foreach($result as $aRow){
                $ticket = $aRow["AccessTicket"];
            }
            return $ticket;
        }
        catch(Exception $ex){
            throw new Exception($ex);
        }
    }   
    
    
    
    function isOtpEnabled($wpUser){
        $otp = $wpUser->get("wp-otp");
        return $otp["enabled"];
    }
    
    
    
    function isDevEnvironment(){
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL);
        if($host === LOCAL_DEV_APP_HOST){
            return true;
        }
        return false;
    }   
    
    
    
    function isEditor($wpUser){
        /*** Check if the user has an editor role ***/
        for($i = 0; $i < count($wpUser->roles); $i++){
            if(strpos($wpUser->roles[$i],  SARON_ROLE_PREFIX . SARON_ROLE_EDITOR) !== FALSE){ // CHECK IF THE USER IS A MEMBER OF THE GROUP  (test)saron_edit
                return true;
            }
        } 
        return false;
    }

    
    
    function isOrgEditor($wpUser){
        /*** Check if the user has an org role ***/
        for($i = 0; $i < count($wpUser->roles); $i++){
            if(strpos($wpUser->roles[$i],  SARON_ROLE_PREFIX . SARON_ROLE_ORG) !== FALSE){ // CHECK IF THE USER IS A MEMBER OF THE GROUP  (test)saron_edit
                return true;
            }
        } 
        return false;        
    }
    
    
    
    function isViewer($wpUser){
        /*** Check if the user has an org role ***/
        for($i = 0; $i < count($wpUser->roles); $i++){
            if(strpos($wpUser->roles[$i],  SARON_ROLE_PREFIX . SARON_ROLE_VIEWER) !== FALSE){ // CHECK IF THE USER IS A MEMBER OF THE GROUP  (test)saron_edit
                return true;
            }
        } 
        return false;        
    }
    
    
    
    function getRole($wpUser){
        if(isEditor($wpUser)){
            return SARON_ROLE_EDITOR;
        }
        if(isOrgEditor($wpUser) ){
            return SARON_ROLE_ORG;
        }
        if(isViewer($wpUser) ){
            return SARON_ROLE_VIEWER;                
        }
        return null;
    }
    

    
    function getRoleDisplayName($wpUser){
        if(isEditor($wpUser) ){
            return SARON_DISPLAY_NAME_EDITOR;
        }
        if(isOrgEditor($wpUser) ){
            return SARON_DISPLAY_NAME_ORG;
        }
        if(isViewer($wpUser) ){
            return SARON_DISPLAY_NAME_VIEWER;                
        }
        return null;
    }
    
    
