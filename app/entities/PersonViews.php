<?php
require_once 'config.php'; 
require_once SARON_ROOT . 'app/database/queries.php'; 

class PersonViews {
    
    function getPersonViewSql($tableview, $saronUser){
        switch ($tableview){
        case "member":
            return $this->selectPeople() . " " . $saronUser->getRoleSql();
        case "baptist":
            return $this->selectBirthday();
        default:    
            return $this->selectPeople() . " " . $saronUser->getRoleSql();
        }
    }    
}
