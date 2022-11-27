<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class News extends SuperEntity{
    
    private $severity;
    private $information;
            
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->severity = (int)filter_input(INPUT_POST, "severity", FILTER_SANITIZE_NUMBER_INT);
        $this->information = (String)filter_input(INPUT_POST, "information", FILTER_SANITIZE_STRING);
    }
    
    function select($id = -1){
        $select = "SELECT *, id as Id, " . $this->saronUser->getRoleSql(false) . " ";
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , "FROM News ", "", $this->getSortSql(), $this->getPageSizeSql(), RECORDS);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM News ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }
    function checkNewData(){
        $error = array();
        $error["Result"] = "OK";
        $error["Message"] = "";

        if(strlen($this->information) < 5){
            $error["Message"] = "Glöm inte att skriva ett meddelande om minst fem tecken!";
        }
        
        if(strlen($error["Message"])>0){
            $error["Result"] = "ERROR";
            return json_encode($error);
        }
        
        return true;
    }

    function insert(){
        $checkResult = $this->checkNewData();
        if($checkResult!==true){
            return $checkResult;
        }
        $sqlInsert = "INSERT INTO News (information, severity, writer) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->information . "', ";
        $sqlInsert.= "'" . $this->severity . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "')";
        $id = $this->db->insert($sqlInsert, "News", "id", "Nyhet","Tidpunkt", null, $this->saronUser);
        $result =  $this->select($id);
        return $result;
    }
    
    
    function update(){
        $checkResult = $this->checkNewData();
        if($checkResult!==true){
            return $checkResult;
        }
        $update = "UPDATE News ";
        $set = "SET ";        
        $set.= "information='" . $this->information . "', ";        
        $set.= "severity='" . $this->severity . "', ";        
        $set.= "writer='" . $this->saronUser->getDisplayName() . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where, 'News', 'id', $this->id, 'Nyhet','Tidpunkt', null, $this->saronUser);        
        return $this->select($this->id);
    }

    function delete(){
        $sql = "delete from News where id=" . $this->id;
        return $this->db->delete($sql, "News", 'id', $this->id, "Nyhet","Tidpunkt", null, $this->saronUser);
    }
}
