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
        $sql = "(" . $tableAlias . ".DateOfBaptism is not null or " . $tableAlias . ".CongregationOfBaptism is not null) ";
        return $sql;        
    }
    
    
    function getHasEngagement($tableAlias = "People"){
        return "(SELECT Count(*) from Org_Pos as Pos Where " . $tableAlias . ".Id = Pos.People_FK > 0) ";
        
    }
    
    
    function getIsAnonymizedSQL($tableAlias = "People"){
        $sql = $tableAlias . ".DateOfAnonymization is not null ";
        return $sql;
    }
    
    
    function getIsNullStateSQL($tableAlias = "People"){
        $sql = $tableAlias . ".Id is null ";
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
        $select = "SELECT MemberState.*, Amount, ";
        $select.= $this->saronUser->getRoleSql(false) ;

        $from = "FROM MemberState ";
        $from.= "left outer join (select count(*) as Amount, MemberStateId ";
        $from.= "from view_people_memberstate as People GROUP BY MemberStateId) as MemberStates on Id = MemberStateId  ";
        
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