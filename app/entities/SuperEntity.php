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

//  web form data
    protected $groupId;

// jTable Parameter
    protected $jtPageSize;
    protected $jtStartIndex;
    protected $jtSorting;

// postData and urlParamterData
    protected $id;
    protected $appCanvasName;
    protected $parentId;
    protected $appCanvasPath;
    protected $source;
    protected $resultType;
    
// Only urlParamterData
    protected $field;

//  
    protected $tableView;

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
        $this->appCanvasName = (String)filter_input(INPUT_POST, "AppCanvasName", FILTER_SANITIZE_STRING);    
        if(strlen($this->appCanvasName) === 0){
            $this->appCanvasName = (String)filter_input(INPUT_GET, "AppCanvasName", FILTER_SANITIZE_STRING);    
        }
        
        $this->field = (String)filter_input(INPUT_GET, "Field", FILTER_SANITIZE_STRING);

        $this->appCanvasPath = (String)filter_input(INPUT_POST, "AppCanvasPath", FILTER_SANITIZE_STRING);
        if(strlen($this->appCanvasPath) === 0){
            $this->appCanvasPath = (String)filter_input(INPUT_GET, "AppCanvasPath", FILTER_SANITIZE_STRING);
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
        $this->source = (String)filter_input(INPUT_POST, "Source", FILTER_SANITIZE_STRING);
        if(strlen($this->source) === 0){
            $this->source = (String)filter_input(INPUT_GET, "Source", FILTER_SANITIZE_STRING);
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
        if($this->groupId === 2 and 
                ($this->tableView === TABLE_NAME_PEOPLE or 
                $this->tableView === TABLE_NAME_MEMBER or 
                $this->tableView === TABLE_NAME_BAPTIST or 
                $this->tableView === TABLE_NAME_KEYS or
                $this->tableView === TABLE_NAME_TOTAL)
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

    
    
    function getAppCanvasSql($continiue = true){
        return $this->getAppCanvasPathSql(true) . $this->getAppCanvasNameSql($continiue);        
    }
    
    
    
    private function getAppCanvasPathSql($continiue = true){
    
        $sql = "";
        if(strlen($this->appCanvasPath) > 0){   
            $sql = "'" . $this->appCanvasPath . "'";
        }
        else{
            $sql = "'AppCanvasPath is missing '";
        }
        
        $sql.=  " AS AppCanvasPath";
        
        if($continiue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";
        }
        return $sql;
    }
    
    
    
    private function getAppCanvasNameSql($continiue = true){
        $sql = "";
        if(strlen($this->appCanvasName) > 0){   
            $sql = "'" . $this->appCanvasName . "'";
        }
        else{
            $sql = "'AppCanvasName is missing '";
        }
        
        $sql.=  " AS AppCanvasName";
        
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
