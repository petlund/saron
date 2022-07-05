<?php
function getMenyLink($pageUri, $fileName, $tableName, $linkTitle){
    $path = '<a href=/'; 
    $path.=SARON_URI;
    $path.=$pageUri;
    $path.=$fileName;
    $path.='?TableName=';
    $path.=$tableName;
    $path.='>';
    $path.=$linkTitle;
    $path.='</a>';

    return $path;    
}

