<?php

function getAppCanvasName($defaultValue = "AppCanvasName Missing"){
    $appCanvasName = (String)filter_input(INPUT_GET, "AppCanvasName", FILTER_SANITIZE_STRING);    
    $val = $defaultValue;
    
    if(strlen($appCanvasName)>0){
        $val = $appCanvasName;
    }
        
    return $val;
}