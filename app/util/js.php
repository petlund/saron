<?php
    require_once "config.php";
    require_once SARON_ROOT . "app/util/js_version_prefix.php";
    
    function getJsAppDistPath($jsUri, $jsFileName){
        if(JS_VERSION_PREFIX !== "JS_VERSION_PREFIX" ){
            return SARON_URI . APP_JS . $jsUri . JS_DIST . JS_VERSION_PREFIX . $jsFileName;            
        }
        else{
            return SARON_URI . APP_JS . $jsUri . $jsFileName;                        
        }

    }
