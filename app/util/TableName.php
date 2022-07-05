<?php

function getTableName(){
    $tableName = (String)filter_input(INPUT_GET, "TableName", FILTER_SANITIZE_STRING);    
    return $tableName;
}