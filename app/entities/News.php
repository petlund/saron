<?php

require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class News extends SuperEntity{

    private $jtSorting;
    private $jtPageSize;
    private $jtStartIndex;
    private $sqlLimit;
    private $sqlOrderBy;
    private $db;
    private $saronUser;
    private $id;
    private $information;
            
    function __construct($db, $saronUser){

        $this->db = $db;
        $this->saronUser = $saronUser;
        
        $this->jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        
        if(Strlen($this->jtSorting)>0){
            $this->sqlOrderBy = "ORDER BY " . $this->jtSorting . " ";
        }
        else{
            $this->sqlOrderBy = "";
        }

        $this->jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $this->jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        
        if($this->jtPageSize>0){
            $this->sqlLimit = "LIMIT " . $this->jtStartIndex . "," . $this->jtPageSize . ";";
        }
        else{    
            $this->sqlLimit = "";
        }      
        
        $this->information = (String)filter_input(INPUT_POST, "information", FILTER_SANITIZE_STRING);
        
        $this->id = (int)filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
    }
    
    function select($id = -1){
        $select = "SELECT * " . $this->saronUser->getRoleSql() . " ";
        if($id < 0){
            return $this->db->select($this->saronUser, $select , "FROM News ", "", $this->sqlOrderBy, $this->sqlLimit, "Records");        
        }
        else{
            return $this->db->select($this->saronUser, $select , "FROM News ", "WHERE id = " . $id . " ", $this->sqlOrderBy, $this->sqlLimit, "Records");        
        }
    }

    function insert(){
        $sqlInsert = "INSERT INTO News (information, writer) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->information . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "')";
        
        $id = $this->db->insert($sqlInsert, "News", "id");
        return $this->select($id);
    }
    
    
    function update(){
        $update = "UPDATE News ";
        $set = "SET ";        
        $set.= "information='" . $this->information . "', ";        
        $set.= "writer='" . $this->saronUser->getDisplayName() . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }

    function delete(){
        return $this->db->delete("delete from News where id=" . $this->id);
    }
}