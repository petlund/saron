<?php

$tableView = (String)filter_input(INPUT_GET, "tableview", FILTER_SANITIZE_STRING);
    
echo "id=search_" . $tableView;