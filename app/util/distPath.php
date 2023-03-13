<?php
    require_once "config.php";
    define("PREFIX_FILE", SARON_ROOT . "app/util/js_version_prefix.php");
    if(file_exists(PREFIX_FILE)){
        require_once PREFIX_FILE;
    }
    
    function getDistPath($uri, $fileName){
        if(file_exists(PREFIX_FILE)){
            return SARON_URI . $uri . DIST_URI . JS_VERSION_PREFIX . $fileName;            
        }
        else{
            return SARON_URI . $uri . $fileName;                        
        }

    }