<?php
require_once "config.php";
require_once SARON_ROOT . "app/access/cookie.php";
require_once SARON_ROOT . "app/entities/SaronUser.php";
require_once SARON_ROOT . "app/database/db.php";

    define( 'WP_USE_THEMES', false );

    function authenticate() { // call only from login.php 
        require_once WP_ROOT . "wp-load.php";

        /*** AUTHENTICATE WP LOGIN ATTEMPT ***/
	$wpUser = wp_signon();
        
        $_POST = array();
        
	if ( is_wp_error( $wpUser ) ) {
            return false;
	} 
        
        /*** INITIATING PHP SESSION ***/
        if ( ! session_id() ) {
                session_start();
        }
        
        
        try{
            if(isSaronUser($wpUser)){
                createSaronSessionUser($wpUser);
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
        $ticket = getTicket();
        removeSaronCookie();
        try{
            $db = new db();
            $wpUser = wp_get_current_user();
            $db->cleanSaronUser($wpUser->ID);
        } 
        catch (Exception $ex) {
            ;
        }
        finally{
            wp_logout();
        }
    }
    
    
    
    function isSaronUser($wpUser){
        if(isOtpEnabled($wpUser) || isDevEnvironment($wpUser)){
            if(getRole($wpUser) !== null){
                return true;
            }
        }
        return false;
    }
    
 
    
    function createSaronSessionUser($wpUser){
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

        $db = new db();
        $ticket = $db->storeSaronSessionUser($wp_id, $editor, $org_editor, $userDisplayName);
        setSaronCookie($ticket);
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
        if(isOrgEditor($wpUser) ){
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
        if(isOrgEditor($wpUser) ){
            return SARON_DISPLAY_NAME_VIEWER;                
        }
        return null;
    }
    
    
//############# OLD #############################
//        function notPermittedMessage(){
//        $error = array();
//        $error["Result"] = "ERROR";
//        $error["Message"] = "Du har inte rättigheter att göra denna åtgärd, eller så har du blivit utloggad.";
//        echo json_encode($error);                
//    }
//    
//  
//    
//    function checkWPSeesion(){
//        $requireEditor = false;
//        $requireOrg = false;
//        $success=false;
//	$saronUser = new SaronUser(wp_get_current_user());
//        $loginUri = SARON_URI . LOGOUT_URU;
//        $host = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL);
//        
//  
//        $https = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_URL); //  !== "on"
//        
//        if($host !== LOCAL_DEV_APP_HOST){
//            if(is_ssl()){
//                if(isPermitted($saronUser, $requireEditor, $requireOrg)){
//                    $success = true;
//                }
//                else{
//                    header("Location: https://" . SARON_URI . LOGOUT_URI);
//                    wp_logout();
//                    $success = false;
//                }
//            }
//            else{ 
//                header("Location: http://" . SARON_URI . LOGOUT_URI);                    
//                wp_logout();
//                $success = false;
//            }
//        }
//        else{ //is local host
//            if(isPermitted($saronUser, $requireEditor, $requireOrg)){
//                $success = true;
//            }
//            else{
//                header("Location: http://" . SARON_URI . LOGOUT_URI);   
//                wp_logout();
//                $success = false;
//            }
//        }
//        return $success;        
//    }
//    
//       
//    
//    function isLoggedIn($viewer=0, $editor=0, $org_editor=0) {
//        if(hasValidSaronSession()){
//            return true;
//        }
//        return false;
//        
//        
////        if(strlen($ticket)>0){
//            if(checkTicket($ticket, $viewer, $editor, $org_editor)){
//                $ticket = updateSaronUserSession($ticket);
//                return $ticket;
//            }
//            else{
//                return null;
//            }
//        }
//        else{
//            $ticket = createSaronSessionUser();
//            return $ticket;
//        }
//    }
//
//    
//    
//     function isPermitted($saronUser, $requireEditor, $requireOrg){
//        require_once WP_ROOT . "wp-load.php";
//        
//        if(! session_id()){
//            return false;
//        }
//        
//        $userLoggedIn = is_user_logged_in();
//        $sUser = $saronUser->isSaronUser();
//        $editor = $saronUser->isEditor();
//        $orgEditor = $saronUser->isOrgEditor();
//        
//        if($userLoggedIn){
//            if($sUser){
//                if($requireEditor){
//                    return $editor;
//                }
//                else if($requireOrg){
//                    return $orgEditor || $editor;
//                }
//                else{
//                    return true;
//                }
//            }            
//        }
//        else{
//            return false;
//        }
//    }
//    
//   