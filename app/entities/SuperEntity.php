<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbstractEntity
 *
 * @author peter
 */

require_once SARON_ROOT . "app/util/GlobalConstants_php.php";


class SuperEntity {
    protected $id;
    protected $db;
    protected $saronUser;
    protected $groupId;
    protected $jtPageSize;
    protected $jtStartIndex;
    protected $jtSorting;
    protected $tableView;
    protected $tableViewId;
    protected $tablePath;
    protected $parentId;
    protected $resultType;
    protected $field;


    protected function __construct($db, $saronUser) {
        $this->db = $db;
        $this->saronUser = $saronUser;

        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        IF($this->id < 1){
            $this->id = (int)filter_input(INPUT_GET, "Id", FILTER_SANITIZE_NUMBER_INT);
        }
        $this->jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $this->jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        $this->jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
            
        $this->groupId = (int)filter_input(INPUT_POST, "groupId", FILTER_SANITIZE_NUMBER_INT);    
        
        $this->uppercaseSearchString = strtoupper((String)filter_input(INPUT_POST, "searchString", FILTER_SANITIZE_STRING));

        
        // POST for table load and GET for Options
        $this->tableViewId = (String)filter_input(INPUT_POST, "TableViewId", FILTER_SANITIZE_STRING);    
        if(strlen($this->tableViewId) === 0){
            $this->tableViewId = (String)filter_input(INPUT_GET, "TableViewId", FILTER_SANITIZE_STRING);    
        }
        if(strlen($this->tableViewId) > 0){
            if(substr($this->tableViewId, 0, 1) === "#"){
                $this->tableView = substr($this->tableViewId, 1, strlen($this->tableViewId));
            }
            else{
                $this->tableView = $this->tableViewId;
            }
        }
        
        $this->field = (String)filter_input(INPUT_GET, "Field", FILTER_SANITIZE_STRING);

        $this->tablePath = (String)filter_input(INPUT_POST, "TablePath", FILTER_SANITIZE_STRING);
        if(strlen($this->tablePath) === 0){
            $this->tablePath = (String)filter_input(INPUT_GET, "TablePath", FILTER_SANITIZE_STRING);
        }
        $this->source = (String)filter_input(INPUT_POST, "Source", FILTER_SANITIZE_STRING);
        if(strlen($this->source) === 0){
            $this->source = (String)filter_input(INPUT_GET, "Source", FILTER_SANITIZE_STRING);
        }
        $this->parentId = (int)filter_input(INPUT_POST, "ParentId", FILTER_SANITIZE_NUMBER_INT);
        if($this->parentId === 0){
            $this->parentId = (int)filter_input(INPUT_GET, "ParentId", FILTER_SANITIZE_NUMBER_INT);
        }
        $this->resultType = (String)filter_input(INPUT_POST, "ResultType", FILTER_SANITIZE_STRING);
        if(strlen($this->resultType) === 0){
            $this->resultType = (String)filter_input(INPUT_GET, "ResultType", FILTER_SANITIZE_STRING);
        }
//        $this->openChildTable = (String)filter_input(INPUT_POST, "OpenChildTable", FILTER_SANITIZE_STRING);
//        if(strlen($this->openChildTable) === 0){
//            $this->openChildTable = (String)filter_input(INPUT_GET, "OpenChildTable", FILTER_SANITIZE_STRING);
//            if(strlen($this->openChildTable) === 0){
//                $this->openChildTable = false;
//            }
//        }

        
    }

    
    
//    protected function getOpenChildSql($continue = false){
//        $answ = $this->openChildTable . ' as "OpenChildTable"';
//        if($continue){
//            return $answ . ", ";
//        }
//        else{
//            return $answ;            
//        }
//    }
//    
//    
//    
    protected function getId($entityId, $clientId){
        if($entityId > 0){
            return $entityId;
        }
        else if($clientId > 0){
            return $clientId;
        }
        else{
            return -1;
        }
    }

    
    protected function getSortSql(){
        $sqlOrderBy = "";
        if($this->groupId === 2 and 
                ($this->tableView === TABLE_VIEW_PEOPLE or 
                $this->tableView === TABLE_VIEW_MEMBER or 
                $this->tableView === TABLE_VIEW_BAPTIST or 
                $this->tableView === TABLE_VIEW_KEYS or
                $this->tableView === TABLE_VIEW_TOTAL)
            ){
            $sqlOrderBy = "ORDER BY Updated desc ";            
        }
        else if(Strlen($this->jtSorting)>0){
            $sqlOrderBy = "ORDER BY " . $this->jtSorting . " ";
        }
        else{ 
            $sqlOrderBy = "";         
        }
        return $sqlOrderBy;
    }


    protected function getPageSizeSql(){
        if($this->jtPageSize === 0){
            return "";
        }
        return "LIMIT " . $this->jtStartIndex . ", " . $this->jtPageSize . ";";
    }        


    
    protected function getZeroToNull($nr){
        if($nr === null){
            return 'null';
        }
        if($nr===0){
            return 'null';
        }
        else{
            return $nr;
        }    
        
    }
    
    protected function setParentAlias($field, $continue = false){
        $sql = $field . " as ParentId";
        if($continue){
            return $sql . ", ";
        }
        return $sql . " ";
    }


    function getEncryptedSqlString($str){
        if(strlen($str)>0){
            return "AES_ENCRYPT('" . $this->salt() . $str . "', " . PKEY . ")";
        }
        else{
            return 'null';                    
        }
    }
    
    
    function getSqlString($str){
        if(strlen($str)>0){
            return "'" . $str . "'";
        }
        else{
            return 'null';                    
        }
    }


    function getSqlDateString($str){
        if(strlen($str)>0){
            return "'" . $str . "'";
        }
        else{
            return 'null';                    
        }
    }

    
    
    function getTablePathSql($continiue = true){
        $sql = "";
        if(strlen($this->tablePath) > 0){   
            $sql = "'" . $this->tablePath . "'";
        }
        else{
            $sql = "'TablePath is missing '";
        }
        
        $sql.=  " AS TablePath";
        
        if($continiue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";
        }
        return $sql;
    }
    
    
    function salt(){        
        $abc = "!#$%&()*+,-./0123456789:;=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~";
        $str = "";
        while(strlen($str)<SALT_LENGTH-1){
            $str.= substr($abc, rand(0, strlen($abc)), 1);
        }
        return $str;
    }   
    
 
    function setUserRoleInQuery($saronUser){
        $alias = " as user_role ";
        $sql = "'";
        if($saronUser->isEditor()){
            $sql.= SARON_ROLE_EDITOR . "'" . $alias;
        }
        else{
            $sql.= SARON_ROLE_VIEWER . "'" . $alias;            
        }
        return $sql;
    }
     
}
