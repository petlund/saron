<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class ChangeLog extends SuperEntity{
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
    }
    
    function select($id = -1){
        $select = "SELECT *, id as Id, " . $this->saronUser->getRoleSql(false) . " ";
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , "FROM Changes ", "", $this->getSortSql(), $this->getPageSizeSql(), RECORDS);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM Changes ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }
//    function checkNewData(){
//        $error = array();
//        $error["Result"] = "OK";
//        $error["Message"] = "";
//
//        if(strlen($this->information) < 5){
//            $error["Message"] = "Glöm inte att skriva ett meddelande om minst fem tecken!";
//        }
//        
//        if(strlen($error["Message"])>0){
//            $error["Result"] = "ERROR";
//            return json_encode($error);
//        }
//        
//        return true;
//    }
//
//    function insert(){
//        $checkResult = $this->checkNewData();
//        if($checkResult!==true){
//            return $checkResult;
//        }
//        $sqlInsert = "INSERT INTO News (information, severity, writer) ";
//        $sqlInsert.= "VALUES (";
//        $sqlInsert.= "'" . $this->information . "', ";
//        $sqlInsert.= "'" . $this->severity . "', ";
//        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "')";
//        
//        $id = $this->db->insert($sqlInsert, "News", "id");
//        $result =  $this->select($id);
//        return $result;
//    }
//    
//    
//    function update(){
//        $checkResult = $this->checkNewData();
//        if($checkResult!==true){
//            return $checkResult;
//        }
//        $update = "UPDATE News ";
//        $set = "SET ";        
//        $set.= "information='" . $this->information . "', ";        
//        $set.= "severity='" . $this->severity . "', ";        
//        $set.= "writer='" . $this->saronUser->getDisplayName() . "' ";
//        $where = "WHERE id=" . $this->id;
//        $this->db->update($update, $set, $where);
//        return $this->select($this->id);
//    }
//
//    function delete(){
//        return $this->db->delete("delete from News where id=" . $this->id);
//    }
}
