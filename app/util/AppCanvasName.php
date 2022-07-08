<?php

function getAppCanvasName(){
    $appCanvasName = (String)filter_input(INPUT_GET, "AppCanvasName", FILTER_SANITIZE_STRING);    
    return $appCanvasName;
}