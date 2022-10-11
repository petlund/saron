<?php
function getMenyLink($pageUri, $fileName, $appCanvasName, $linkTitle, $newPage=false){
    $path = '<a href=/'; 
    $path.=SARON_URI;
    $path.=$pageUri;
    $path.=$fileName;
    $path.='?AppCanvasName=';
    $path.=$appCanvasName;
    if($newPage){
        $path.=' target="_empty"';
    }
    $path.='>';
    $path.=$linkTitle;
    $path.='</a>';

    return $path;    
}

