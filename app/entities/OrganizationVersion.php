<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationVersion extends SuperEntity{
    
    private $id;
    private $decision_date;
    private $information;
            
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
        $this->information = (String)filter_input(INPUT_POST, "information", FILTER_SANITIZE_STRING);
        $this->decision_date = (String)filter_input(INPUT_POST, "decision_date", FILTER_SANITIZE_STRING);
    }
    
    function select($id = -1, $rec=RECORDS){
        $select = "SELECT *, " . $this->saronUser->getRoleSql(false) . " ";
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , "FROM Org_Version ", "", $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM Org_Version ", "WHERE id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }

    function checkVersionData(){
        $error = array();

        if(strlen($this->information) < 10){
            $error["Result"] = "ERROR";
            $error["Message"] = "Du behöver ge en lite längre förklaring till varför du skapar en ny version av organisationen. ";
            throw new Exception(json_encode($error));
        }
         
        if(strlen($this->decision_date) < 10){
            $error["Result"] = "ERROR";
            $error["Message"] = "Giltigt dataum saknas för beslutstillfället.";
            throw new Exception(json_encode($error));
        }
         
    }
    
    
    function update_Org(){
        $update = "update Org_Pos ";
        $set = "SET PrevPeople_FK = People_FK, ";        
        $set.= "PrevFunction_FK = Function_FK, ";        
        $set.= "PrevOrgPosStatus_FK = OrgPosStatus_FK ";        
        $where = "WHERE OrgPosStatus_FK in (1, 5, 6)";
        $this->db->update($update, $set, $where);
    }


    function  update(){ // TBD
        $this->checkVersionData();
        $update = "update Org_Version ";
        $set = "SET ";        
        $set.= "decision_date = '" . $this->decision_date .  "', ";        
        $set.= "information = '". $this->information . "' ";        
        $where = "WHERE id = "  . $this->id;
        $this->db->update($update, $set, $where);        

        $result =  $this->select($this->id, RECORD);
        return $result;
    }
            
    function insert(){
        $this->checkVersionData();
        $this->update_Org();

        $sqlInsert = "INSERT INTO Org_Version (decision_date, information, UpdaterName) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->decision_date . "', ";
        $sqlInsert.= "'" . $this->information . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Version", "id");
        $result =  $this->select($id, RECORD);
        return $result;
    }
    
}