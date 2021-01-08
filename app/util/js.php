<?php
    require_once "config.php";
    require_once SARON_ROOT . "app/util/js_version_prefix.php";
    
    
    echo "<script>const WP_URI = '" . WP_URI . "';</script>";
    echo "<script>const SARON_URI = '" . SARON_URI . "';</script>";
    echo "<script>const SARON_IMAGES_URI = '" . SARON_IMAGES_URI . "';</script>";
    echo "<script>const FullNameOfCongregation = '" . FullNameOfCongregation . "';</script>";
    echo "<script>const SESSION_EXPIRES = '" . SESSION_EXPIRES . "';</script>";
    
    
    function getJsAppDistPath($jsUri, $jsFileName){
        if(JS_VERSION_PREFIX !== "JS_VERSION_PREFIX" ){
            return SARON_URI . APP_JS . $jsUri . JS_DIST . JS_VERSION_PREFIX . $jsFileName;            
        }
        else{
            return SARON_URI . APP_JS . $jsUri . $jsFileName;                        
        }

    }
