<?php
    require_once "config.php";
    require_once SARON_ROOT . "app/util/js_version_prefix.php";
    
    function getDistPath($uri, $fileName){
        if(JS_VERSION_PREFIX !== "JS_VERSION_PREFIX" ){
            return SARON_URI . $uri . DIST_URI . JS_VERSION_PREFIX . $fileName;            
        }
        else{
            return SARON_URI . $uri . $fileName;                        
        }

    }
