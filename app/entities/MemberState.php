<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/database/queries.php'; 

class MemberState extends SuperEntity{
    
    private $description;
    private $filterUpdate;
    private $filterCreate;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
        $this->filterUpdate = (int)filter_input(INPUT_POST, "FilterUpdate", FILTER_SANITIZE_NUMBER_INT);
        $this->filterCreate = (int)filter_input(INPUT_POST, "FilterCreate", FILTER_SANITIZE_NUMBER_INT);
    }
    
    function getIsMemberSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfMembershipStart is not null AND " . 
               $tableAlias . ".DateOfDeath is null AND " .
               $tableAlias . ".DateOfMembershipEnd is null) ";
        
        return $sql;
    }
    
    
    function getIsFriendSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfMembershipStart is null OR (" . 
               $tableAlias . ".DateOfMembershipStart is not null AND " .
               $tableAlias . ".DateOfMembershipEnd is not null)) AND " .
               $tableAlias . ".DateOfDeath is null AND " .
               "UPPER(CONVERT(BINARY " . $this->getFieldSql($tableAlias, null, "LastNameEncrypt", null, true, false) . " USING utf8)) NOT like '%" . ANONYMOUS . "%' AND " .
               $tableAlias . ".DateOfFriendshipStart is not null ";
        return $sql;        
    }
    
    
    function getIsBaptistSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfBaptism is not null "
                . "OR (" . $tableAlias . ".DateOfMembershipStart is not null AND " . $tableAlias . ".DateOfMembershipEnd is not null)) "
                . "AND UPPER(CONVERT(BINARY " . $this->getFieldSql($tableAlias, null, "LastNameEncrypt", null, true, false) . " USING utf8)) NOT like '%" . ANONYMOUS . "%' "
                . "AND " . $tableAlias . ".DateOfDeath is null " 
                . "AND " . $tableAlias . ".DateOfFriendshipStart is null ";
        return $sql;        
    }
    
    
    function getIsVolontaireSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfMembershipStart is null AND " . 
               //$tableAlias . ".DateOfMembershipEnd is null AND " .  
               "UPPER(CONVERT(BINARY " . $this->getFieldSql($tableAlias, null, "LastNameEncrypt", null, true, false) . " USING utf8)) NOT like '%" . ANONYMOUS . "%' AND " .
               $tableAlias . ".DateOfDeath is null AND " .
               $tableAlias . ".DateOfFriendshipStart is null AND " .
               "(SELECT Count(*) from Org_Pos as Pos Where " . $tableAlias . ".Id = Pos.People_FK) > 0) ";
        return $sql;        
    }
    
    
    function getIsDeathSQL($tableAlias = "People"){
        $sql = $tableAlias . ".DateOfDeath is not null ";
        return $sql;        
    }
    
    
    function getIsRegistratedSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfDeath is null AND " . 
               $tableAlias . ".DateOfBaptism is null AND " .
               $tableAlias . ".DateOfMembershipStart is null AND " .
               $tableAlias . ".DateOfMembershipEnd is null AND " .
               "UPPER(CONVERT(BINARY " . $this->getFieldSql($tableAlias, null, "LastNameEncrypt", null, true, false) . " USING utf8)) NOT like '%" . ANONYMOUS . "%' AND " .
               $tableAlias . ".DateOfFriendshipStart is null AND " .
               "(SELECT Count(*) from Org_Pos as Pos Where " . $tableAlias . ".Id = Pos.People_FK) = 0) ";
        return $sql;
    }
    
    
    function getIsAnonymizedSQL($tableAlias = "People"){
        $sql = "UPPER(CONVERT(BINARY " . $this->getFieldSql($tableAlias, null, "LastNameEncrypt", null, true, false) . " USING utf8)) like '%" . ANONYMOUS . "%'";
        return $sql;
    }
    
    
    function getIsNullStateSQL($tableAlias = "People"){
        $sql = $tableAlias . ".Id is null ";
        return $sql;
    }
    
    
    
    function getMemberStateSql($tableAlias = "People", $fieldAlias ="", $continue=false){//Memberstatelogic
        $sql ="(SELECT MemberState.Name FROM MemberState Where MemberState.Id = ";
        $sql.=$this->getMemberStateIndexSql($tableAlias, null, false);
        $sql.=") ";

        if(strlen($fieldAlias) > 0){
            $sql.= " AS " . $fieldAlias;
        }
        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;        
    }
    

    function getMemberStateIndexSql($tableAlias = "People", $fieldAlias="", $continue=false){//Memberstatelogic
        $sql="Case ";
        $sql.="WHEN " . $this->getIsNullStateSQL($tableAlias) . " Then 0 ";
        $sql.="WHEN " . $this->getIsRegistratedSQL($tableAlias) . " Then 1 ";
        $sql.="WHEN " . $this->getIsDeathSQL($tableAlias) . " Then 5 ";
        $sql.="WHEN " . $this->getIsMemberSQL($tableAlias) . " Then 2 ";
        $sql.="WHEN " . $this->getIsAnonymizedSQL($tableAlias) . " THEN 4 ";
        $sql.="WHEN " . $this->getIsVolontaireSQL($tableAlias) . " then 6 ";
        $sql.="WHEN " . $this->getIsFriendSQL($tableAlias) . " Then 7 ";
        $sql.="WHEN " . $this->getIsBaptistSQL($tableAlias) . " Then 3 ";
        $sql.="else -1 ";
        $sql.="END";

        if(strlen($fieldAlias) > 0){
            $sql.= " AS " . $fieldAlias;
        }
        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;        
    }    
    
       

    function getFilteredMemberStateSql($tableAlias = "People", $fieldAlias, $continue, $source){ //Memberstatelogic
        
        $sql1 = "";
        if($source === SOURCE_EDIT){
            $sql1 = "IF(MemberState.FilterUpdate = '1', true, false) AND ";
        }
        else{
            $sql1 = "true AND ";            
        }
        
        $sql2 = "";
        if($source === SOURCE_CREATE){
            $sql2 = "IF(MemberState.FilterCreate = '1', true, false) ";            
        }
        else{
            $sql2 = "true ";            
        }
        
        
        $sql = "(SELECT ";
        $sql.= $sql1;
        $sql.= $sql2;
        $sql.= "FROM MemberState Where MemberState.Id = ";
        $sql.= $this->getMemberStateIndexSql($tableAlias, null, false);
        $sql.= ") ";

        if(strlen($fieldAlias) > 0){
            $sql.= " AS " . $fieldAlias;
        }
        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;        
        
    }
    

    function select($id = -1){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectAsOptions();       
        default:
            return $this->selectDefault($id);
        }
    }
    
    
    function selectDefault($id){
        $select = "SELECT MemberState.*, Amount, " . $this->saronUser->getRoleSql(false) ;
        $from = "FROM MemberState right outer join (select count(*) as Amount, " . $this->getMemberStateIndexSql() 
                . "as MemberStateId from People GROUP BY MemberStateId) as MemberStates on Id = MemberStateId  ";
   
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , $from, "", $this->getSortSql(), $this->getPageSizeSql(), RECORDS);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "WHERE Id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }
    
    
    function selectAsOptions(){
        $select = "Select Id as Value, Name as DisplayText ";
        $result = $this->db->select($this->saronUser, $select , "FROM MemberState ", "", $this->getSortSql(), $this->getPageSizeSql(), OPTIONS);    
        return $result;        
    }
    

    
    function update(){
        $update = "UPDATE MemberState ";
        $set = "SET ";        
        $set.= "Description='" . $this->description . "', ";        
        $set.= "FilterCreate=" . $this->filterCreate . ", ";        
        $set.= "FilterUpdate=" . $this->filterUpdate . ", ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "', ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "' ";        
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }



    function delete(){
    }
}