<?php
function getMenyLink($pageUri, $fileName, $appCanvasName, $linkTitle){
    $path = '<a href=/'; 
    $path.=SARON_URI;
    $path.=$pageUri;
    $path.=$fileName;
    $path.='?AppCanvasName=';
    $path.=$appCanvasName;
    $path.='>';
    $path.=$linkTitle;
    $path.='</a>';

    return $path;    
}

