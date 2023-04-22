<?php
require_once "config.php";
require_once SARON_ROOT . "app/logging/SysLog.php";
require_once SARON_ROOT . 'app/entities/SaronMetaUser.php'; 
require_once SARON_ROOT . "app/access/SaronCookie.php";
require_once SARON_ROOT . "app/access/Ticket.php";
require_once SARON_ROOT . "app/database/db.php";
require_once SARON_ROOT . "app/database/BusinessLogger.php";

    define( 'WP_USE_THEMES', false );

    function authenticate() { // call only from login.php 
        $syslog =  new SysLog();
        require_once WP_ROOT . "wp-load.php";


        /*** AUTHENTICATE WP LOGIN ATTEMPT ***/
	$wpUser = wp_signon();
        
	if ( is_wp_error( $wpUser ) ) {
            $error_message = "";
            $usedLoginName = (String)filter_input(INPUT_POST, "log", FILTER_SANITIZE_STRING);
            if(strlen($usedLoginName) ===  0){
                $error_message = "<b>Felaktigt inloggningsförsök</b><br>Användarnamn saknas.";
                $usedLoginName = "...";
            }
            else{
                $error = $wpUser->get_error_message("invalid_username");
                if(strlen($error) >  0){
                    $error_message = "<b>Felaktigt inloggningsförsök</b><br>Okänd användare.";
                }
                else{
                    $error_message = "<b>Felaktigt inloggningsförsök</b>";                
                }
            }
            
            $db = new db();
            $syslog->saron_dev_log(LOG_INFO, "wp-authenticate", "authenticate", $error_message, null);
            $saronMetaUser = new SaronMetaUser();
            $businessLogger = new BusinessLogger($db, $saronMetaUser);
            $businessLogger->insertLogPost("SaronUser", "WP_ID", -1, "Login error", "Användarnamn", $usedLoginName, $error_message, $saronMetaUser);
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
    

    
    function logout($saronUserLogout){
        require_once WP_ROOT . "wp-load.php";

        try{
            $description = "Logout";
            $saronUser="";
            $db = new db();

            $wpUser = wp_get_current_user();
            if($wpUser->ID > 0){ // IF logged in
                if($saronUserLogout === 'true'){
                    $saronUser = new SaronMetaUser($wpUser->ID, $wpUser->display_name, $wpUser->user_login);
                }
                else{
                    $saronUser = new SaronMetaUser();
                }
                $createlogPost=true;
                deletePersistentSaron($db, $wpUser->ID, $description, $saronUser, $createlogPost, $wpUser);
            }
        } 
        catch (Exception $ex) {
            $syslog =  new SysLog();
            $syslog->saron_dev_log(LOG_DEBUG, "wp-authenticate", "logout", $ex, $sql="");
        }
        finally{
            wp_logout();
            removeSaronCookie();
        }
    }
    
    
    
    function deletePersistentSaron($db, $userId, $description, $saronUser, $createlogPost=true, $wpUser=null){
        $sql = "DELETE FROM SaronUser WHERE WP_ID = " . $userId;
        $db->delete($sql, "SaronUser", "WP_ID", $userId, 'Användarsession', 'Användarnamn', $description, $saronUser, $createlogPost, $wpUser);
    }
    
    
    
    function isSaronUser($wpUser){
//        if($wpUser instanceof WP_User){
            if(isOtpEnabled($wpUser) || isDevEnvironment($wpUser)){
                if(getRole($wpUser) !== null){
                    return true;
                }
            }
//        }
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
        $user_login = $wpUser->user_login;
        $wp_id = $wpUser->ID;

        $ticket = insertSaronSessionUser($wp_id, $userDisplayName, $user_login, $editor, $org_editor );
        setSaronCookie($ticket);
    }
    
    
    
    function insertSaronSessionUser($wp_id, $userDisplayName, $user_login, $editor, $org_editor){
        $db = new db();        
        $description1 = "<b>Borttag av Användarsession</b><br>";
        $description1.= "Städat bort gamla sessioner";
        $system = new SaronMetaUser();
        $wp_user = new SaronMetaUser($wp_id, $userDisplayName, $user_login);
        
        deletePersistentSaron($db, $wp_id, $description1, $system, false );   
        
        $sql = "INSERT INTO SaronUser (AccessTicket, Editor, Org_Editor, WP_ID, UserName, UserDisplayName) values (";
        $sql.= getAccessTicket() . ", "; 
        $sql.= $editor . ", ";
        $sql.= $org_editor . ", ";
        $sql.= $wp_id . ", '";
        $sql.= $user_login . "', '";
        $sql.= $userDisplayName . "') ";

        try{
            $description2 = "Login";
            $lastId = $db->insert($sql, "SaronUser", "Id", 'Användarsession', 'Användarnamn', $description2, $wp_user);
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
    
    
