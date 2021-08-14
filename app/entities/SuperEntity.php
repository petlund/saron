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
    protected $db;
    protected $saronUser;
    protected $groupId;
    protected $jtPageSize;
    protected $jtStartIndex;
    protected $jtSorting;
    protected $tableview;
    protected $tablePath;
    protected $parentId;
    protected $resultType;



    protected function __construct($db, $saronUser) {
        $this->db = $db;
        $this->saronUser = $saronUser;

        $this->jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $this->jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        $this->jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
            
        $this->groupId = (int)filter_input(INPUT_POST, "groupId", FILTER_SANITIZE_NUMBER_INT);    
        $this->uppercaseSearchString = strtoupper((String)filter_input(INPUT_POST, "searchString", FILTER_SANITIZE_STRING));

        
        // POST for table load and GET for Options
        $this->selection = (String)filter_input(INPUT_GET, "selection", FILTER_SANITIZE_STRING);    
        
        $this->tableview = (String)filter_input(INPUT_POST, "tableview", FILTER_SANITIZE_STRING);    
        if($this->tableview !== null){
            $this->tableview = (String)filter_input(INPUT_GET, "tableview", FILTER_SANITIZE_STRING);    
        }
        $this->tablePath = (String)filter_input(INPUT_POST, "TablePath", FILTER_SANITIZE_STRING);
        if($this->tablePath !== null){
            $this->tablePath = (String)filter_input(INPUT_GET, "TablePath", FILTER_SANITIZE_STRING);
        }
        $this->source = (String)filter_input(INPUT_POST, "Source", FILTER_SANITIZE_STRING);
        if($this->source !== null){
            $this->source = (String)filter_input(INPUT_GET, "Source", FILTER_SANITIZE_STRING);
        }
        $this->parentId = (int)filter_input(INPUT_POST, "ParentId", FILTER_SANITIZE_NUMBER_INT);
        if($this->parentId === 0){
            $this->parentId = (int)filter_input(INPUT_GET, "ParentId", FILTER_SANITIZE_NUMBER_INT);
            if($this->parentId === 0){
                $this->parentId=-1;
            }
        }
        $this->resultType = (String)filter_input(INPUT_POST, "ResultType", FILTER_SANITIZE_STRING);
        if($this->resultType !== null){
            $this->resultType = (String)filter_input(INPUT_POST, "ResultType", FILTER_SANITIZE_STRING);
        }
    }

    
    
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
        if($this->groupId === 2 and $this->tableview === "people"){
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

    
    
    function getTablePathSql(){
        if(strlen($this->tablePath) > 0){    
            return "'" . $this->tablePath . "' AS TablePath, ";
        }
        return "";
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
