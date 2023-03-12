<?php
    require_once "config.php";
    $prefixFile = SARON_ROOT . "app/util/js_version_prefix.php";
    if(file_exists($prefixFile)){
        require_once $prefixFile;
    }
    
    function getDistPath($uri, $fileName){
        if(JS_VERSION_PREFIX !== "JS_VERSION_PREFIX" ){
            return SARON_URI . $uri . DIST_URI . JS_VERSION_PREFIX . $fileName;            
        }
        else{
            return SARON_URI . $uri . $fileName;                        
        }

    }
