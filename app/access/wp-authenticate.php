<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once "config.php";
require_once SARON_ROOT . "/app/entities/SaronUser.php";


    init();
    
    function authenticate() {
	/*** AUTHENTICATE LOGIN ATTEMPT ***/
	$user = wp_signon();
	if ( is_wp_error( $user ) ) {
            return false;
	}
        $saronUser = new SaronUser($user);
        if(! $saronUser->isSaronUser()){
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
    
    function isPermitted($saronUser, $requireEditor){
        if(! session_id()){
            return false;
        }
        
        $userLoggedIn = is_user_logged_in();
        $sUser = $saronUser->isSaronUser();
        $editor = $saronUser->isEditor();
        
        return $userLoggedIn && $sUser && ($editor || !$requireEditor);
    }
    
    function notPermittedMessage(){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "Du har inte rättigheter att göra denna åtgärd, eller så har du blivit utloggad.";
        echo json_encode($error);                
    }
    
  
    function isLoggedIn($requireEditor=false) { //
        $success=false;
	    $saronUser = new SaronUser(wp_get_current_user());
        $loginUri = SARON_URI . "app/access/login.php?logout=true";
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL);
        
        $loginPageUrl = $host . "/" . $loginUri;
        $https = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_URL); //  !== "on"
        if($host !== LOCAL_DEV_APP_HOST){
            if(is_ssl()){
                if(isPermitted($saronUser, $requireEditor)){
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
            if(isPermitted($saronUser, $requireEditor)){
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

    
