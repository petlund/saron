<?php

$tableView = (String)filter_input(INPUT_GET, "tableview", FILTER_SANITIZE_STRING);

if(strlen($tableView)==0){
    $tableView = "people";
}
    
echo $tableView;