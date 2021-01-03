<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class News extends SuperEntity{
    
    private $id;
    private $severity;
    private $information;
            
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
        $this->severity = (int)filter_input(INPUT_POST, "severity", FILTER_SANITIZE_NUMBER_INT);
        $this->information = (String)filter_input(INPUT_POST, "information", FILTER_SANITIZE_STRING);
    }
    
    function select($id = -1, $rec=RECORDS){
        $select = "SELECT *, " . $this->saronUser->getRoleSql(false) . " ";
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , "FROM News ", "", $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM News ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), $rec);        
            return $result;
        }
    }

    function insert(){
        $sqlInsert = "INSERT INTO News (information, severity, writer) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->information . "', ";
        $sqlInsert.= "'" . $this->severity . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "')";
        
        $id = $this->db->insert($sqlInsert, "News", "id");
        $result =  $this->select($id, RECORD);
        return $result;
    }
    
    
    function update(){
        $update = "UPDATE News ";
        $set = "SET ";        
        $set.= "information='" . $this->information . "', ";        
        $set.= "severity='" . $this->severity . "', ";        
        $set.= "writer='" . $this->saronUser->getDisplayName() . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    function delete(){
        return $this->db->delete("delete from News where id=" . $this->id);
    }
}
//{"Result":"OK","Record":"{'id':'167', 'news_date':'2020-06-16 18:52:14', 'information':'q', 'writer':'saron utvecklare', 'user_role':'edit'}","TotalRecordCount":"1","user_role":"edit"}"