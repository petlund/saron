<?php

$tableView = (String)filter_input(INPUT_GET, "TableView", FILTER_SANITIZE_STRING);
    
echo $tableView;