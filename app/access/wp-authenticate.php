<?php
require_once "config.php";
    init();
    
    function authenticate() {
	/*** AUTHENTICATE LOGIN ATTEMPT ***/
	$user = wp_signon();
	if ( is_wp_error( $user ) ) {
		return false;
	}
           
        if(! isSaronUser($user)){
            wp_logout();
            return false;
        }

        /*** REDIRECT USER TO PREVIOUS PAGE ***/
	if ( isset( $_SESSION['return_to'] ) ):
		$url = $_SESSION['return_to'];
		unset( $_SESSION['return_to'] );
		header( "location: $url" );
	else:
		header( 'location: /' . SARON_URI );
	endif;
        return true;
    }
    
    function isSaronUser($user){
        /*** Check if the user had a saron role ***/
        for($i = 0; $i < count($user->roles); $i++){
            if(substr($user->roles[$i], 0, strlen(SARON_ROLE_PREFIX)) === SARON_ROLE_PREFIX){ // CHECK IF THE USER IS A MEMBER OF THE GROUP  saron_edit
                $otp = $user->get("wp-otp");
                if($otp["enabled"] || TEST_ENV){ // In test environment OTP enabeled account is not necessary.
                    return true;
                }
            }
        }         
        return false;
    }
    
    
    function isEditor($user){
        /*** Check if the user had an editor role ***/
        for($i = 0; $i < count($user->roles); $i++){
            if(strpos($user->roles[$i],  SARON_ROLE_PREFIX . SARON_ROLE_EDITOR) !== FALSE){ // CHECK IF THE USER IS A MEMBER OF THE GROUP  (test)saron_edit
                return true;
            }
        } 
        return false;
    }
    
    function isPermitted($user, $requireEditor){
        if(! session_id()){
            return false;
        }
        $userLoggedIn = is_user_logged_in();
        $saronUser = isSaronUser($user);
        $editor = isEditor($user);
        
        return $userLoggedIn && $saronUser && ($editor || !$requireEditor);
    }
    
    function notPermittedMessage(){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "Permission denied!";
        echo json_encode($error);                
    }
    
    function isLoggedIn($requireEditor=false) { //
        $success=false;
	$user = wp_get_current_user();
        $loginUri = SARON_URI . "app/access/login.php?logout=true";
        //$loginUri = SARON_URI . "app/access/login.php";
        $loginPageUrl = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL) . "/" . $loginUri;
        $https = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_URL); //  !== "on"
        if(filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL) !== "localhost"){
            if(is_ssl()){
                if(isPermitted($user, $requireEditor)){
                    $success = true;
                }
                else{
                    header("Location: https://" . $loginPageUrl);
                    wp_logout();
                    $success = false;
                }
            }
            else{ 
                header("Location: http://" . $loginPageUrl);                    
                wp_logout();
                $success = false;
            }
        }
        else{ //is local host
            if(isPermitted($user, $requireEditor)){
                $success = true;
            }
            else{
                header("Location: http://" . $loginPageUrl);   
                wp_logout();
                $success = false;
            }
        }
        return $success;
    }

    function init() {        
	/*** INITIATING PHP SESSION ***/
	if ( ! session_id() ) {
		session_start();
	}
	/*** LOADING WORDPRESS LIBRARIES ***/
	define( 'WP_USE_THEMES', false );
	require_once WP_ROOT . "wp-load.php";
    }

    
