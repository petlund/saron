<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
require_once SARON_ROOT . 'app/database/queries.php'; 

class MemberState extends SuperEntity{
    
    private $description;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
    }
    
// ======== STATE SQL =========

    function hasStateFriendshipSQL($tableAlias = "People"){
        $sql = $tableAlias . ".DateOfFriendshipStart > DATE_SUB(NOW(),INTERVAL 400 DAY)";
        return $sql;                
    }


    function hasStateMembershipSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfMembershipStart is not null AND " . 
               $tableAlias . ".DateOfMembershipEnd is null) ";        
        return $sql;
    }
    
    
    function hasStateMembershipEndedSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfMembershipStart is not null AND " . 
               $tableAlias . ".DateOfMembershipEnd is NOT null) ";

        return $sql;                
    }

    function hasStateRegistratedSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfMembershipStart is null AND " . 
               $tableAlias . ".DateOfMembershipEnd is null AND " .
               $tableAlias . ".DateOfBaptism is null) ";

        return $sql;
    }
    
    
// ======== STATE SQL END ======
 
    function getIsDeadSQL($tableAlias = "People"){
        $sql = $tableAlias . ".DateOfDeath is not null ";
        return $sql;        
    }

    
    function getIsEndedFriendshipSQL($tableAlias = "People"){
        $sql = $tableAlias . ".DateOfFriendshipStart < DATE_SUB(NOW(),INTERVAL 365 DAY)";
        return $sql;                
    }
    
        
    function getIsBaptistSQL($tableAlias = "People"){
        $sql = $tableAlias . ".DateOfBaptism is not null ";
        return $sql;        
    }
    
    
    function getHasEngagement($tableAlias = "People"){
        return "(SELECT Count(*) from Org_Pos as Pos Where " . $tableAlias . ".Id = Pos.People_FK > 0) ";
        
    }
    
    
    function getIsVolontaireSQL($tableAlias = "People"){
        $sql = "(" . $tableAlias . ".DateOfMembershipStart is null AND NOT " . 
               $this->getIsAnonymizedSQL($tableAlias) . " AND " .
               $tableAlias . ".DateOfDeath is null AND " .
               $tableAlias . ".DateOfFriendshipStart is null AND " .
               $this->getHasEngagement($tableAlias). ") ";
        return $sql;        
    }

    
    function getIsAnonymizedSQL($tableAlias = "People"){
        $sql = $tableAlias . ".DateOfAnonymization is not null ";
        return $sql;
    }
    
    
    function getIsNullStateSQL($tableAlias = "People"){
        $sql = $tableAlias . ".Id is null ";
        return $sql;
    }
    
    
    
//    function getMemberStateSql($tableAlias = "People", $fieldAlias ="", $continue=false){//Memberstatelogic
//        $sql ="(SELECT MemberState.Name FROM MemberState Where MemberState.Id = ";
//        $sql.=$this->getMemberStateIndexSql($tableAlias, null, false);
//        $sql.=") ";
//
//        if(strlen($fieldAlias) > 0){
//            $sql.= " AS " . $fieldAlias;
//        }
//        if($continue){
//            $sql.= ", ";
//        }
//        else{
//            $sql.= " ";            
//        }
//        return $sql;        
//    }
//    
//
//    function getMemberStateIndexSql($tableAlias = "People", $fieldAlias="", $continue=false){//Memberstatelogic
//        $sql="Case ";
//        $sql.="WHEN " . $this->getIsAnonymizedSQL($tableAlias) . " THEN 4 ";
//        $sql.="WHEN " . $this->getIsDeadSQL($tableAlias) . " Then 5 ";
//        $sql.="WHEN " . $this->hasStateFriendshipSQL($tableAlias) . " Then 7 ";
//        $sql.="WHEN " . $this->hasStateMembershipSQL($tableAlias) . " Then 2 ";
//        $sql.="WHEN " . $this->hasStateMembershipEndedSQL($tableAlias) . " Then 8 ";
//        $sql.="WHEN " . $this->getIsBaptistSQL($tableAlias) . " Then 3 ";
//        $sql.="WHEN " . $this->hasStateRegistratedSQL($tableAlias) . " Then 1 ";
////        $sql.="WHEN " . $this->getIsVolontaireSQL($tableAlias) . " THEN 6 ";
////        $sql.="WHEN " . $this->getIsBaptistSQL($tableAlias) . " Then 3 ";
////        $sql.="WHEN " . $this->getIsNullStateSQL($tableAlias) . " Then 0 ";
//        $sql.="else 1 ";
//        $sql.="END";
//
//        if(strlen($fieldAlias) > 0){
//            $sql.= " AS " . $fieldAlias;
//        }
//        if($continue){
//            $sql.= ", ";
//        }
//        else{
//            $sql.= " ";            
//        }
//        return $sql;        
//    }    
//    
//       
//
//    function getFilteredMemberStateSql($tableAlias = "People", $fieldAlias, $continue, $source){ //Memberstatelogic
//        
//        $sql1 = "";
//        if($source === SOURCE_EDIT){
//            $sql1 = "IF(MemberState.FilterUpdate = '1', true, false) AND ";
//        }
//        else{
//            $sql1 = "true AND ";            
//        }
//        
//        $sql2 = "";
//        if($source === SOURCE_CREATE){
//            $sql2 = "IF(MemberState.FilterCreate = '1', true, false) ";            
//        }
//        else{
//            $sql2 = "true ";            
//        }
//        
//        
//        $sql = "(SELECT ";
//        $sql.= $sql1;
//        $sql.= $sql2;
//        $sql.= "FROM MemberState Where MemberState.Id = ";
//        $sql.= $this->getMemberStateIndexSql($tableAlias, null, false);
//        $sql.= ") ";
//
//        if(strlen($fieldAlias) > 0){
//            $sql.= " AS " . $fieldAlias;
//        }
//        if($continue){
//            $sql.= ", ";
//        }
//        else{
//            $sql.= " ";            
//        }
//        return $sql;        
//        
//    }
    

    function select($id = -1){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectAsOptions();       
        default:
            return $this->selectDefault($id);
        }
    }
    
    
    function selectDefault($id){
        $select = "SELECT MemberState.*, Amount, ";
        $select.= $this->saronUser->getRoleSql(false) ;

        $from = "FROM MemberState ";
        $from.= "right outer join (select count(*) as Amount, ";
        $from.= $this->getMemberStateIndexSql();
        $from.= "as MemberStateId from People GROUP BY MemberStateId) as MemberStates on Id = MemberStateId  ";
        
        switch($this->appCanvasPath){
            case TABLE_NAME_MEMBER_STATE_REPORT:
                $select.= ", "; 
                $select.= $this->getIncludedInReport(DIRECTORY_REPORT, true);
                $select.= $this->getIncludedInReport(BAPTIST_DIRECTORY_REPORT, true);
                $select.= $this->getIncludedInReport(DOSSIER_REPORT, true);
                $select.= $this->getIncludedInReport(SEND_MESSAGES, false);
            break;
        }
   
        if($id < 0){
            $result = $this->db->select($this->saronUser, $select , $from, "", $this->getSortSql(), $this->getPageSizeSql(), RECORDS);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , $from, "WHERE Id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), RECORD);        
            return $result;
        }
    }
    
    
    
    function getIncludedInReport($reportName, $continiue = false){
        $sqlIn = "";
        switch($reportName){
            case DIRECTORY_REPORT: 
                $sqlIn = "(2)";
            break;
            case BAPTIST_DIRECTORY_REPORT: 
                $sqlIn = "(2,3, 8)";
            break;
            case DOSSIER_REPORT: 
                $sqlIn = "(1,2,3,4,5, 6, 7, 8)";
            break;
            case SEND_MESSAGES: 
                $sqlIn = "(2,6,7)";
            break;
            case POTENTIAL_VOLONTAIRE: 
                $sqlIn = "(1,7)";
            break;
            default:
            $sqlIn = "(-1)";
        }
        $sql = "(select IF(MemberState.Id in " . $sqlIn . ", 1, 0)) as " . $reportName;
        
        if($continiue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;
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
        $set.= "Updater='" . $this->saronUser->WP_ID . "', ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "' ";        
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }



    function delete(){
    }
}