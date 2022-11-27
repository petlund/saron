<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationVersion extends SuperEntity{
    
    private $decision_date;
    private $information;
    private $memberState;
            
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->information = (String)filter_input(INPUT_POST, "information", FILTER_SANITIZE_STRING);
        $this->decision_date = (String)filter_input(INPUT_POST, "decision_date", FILTER_SANITIZE_STRING);
        $this->memberState = new MemberState($db, $saronUser);
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

        if(strlen($this->information) < 5){
            $error["Result"] = "ERROR";
            $error["Message"] = "Namnge beslutsmötet. ";
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
        $set.= "PrevOrgSuperPos_FK = OrgSuperPos_FK, ";        
        $set.= "PrevOrgPosStatus_FK = OrgPosStatus_FK, ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater=" . $this->saronUser->WP_ID . " ";
        $where = "WHERE OrgPosStatus_FK = 1 ";
        $this->db->update($update, $set, $where, 'Org_Version', 'Id', $this->id, 'Organisationsversion','Beslutsdatum', null, $this->saronUser);
    }
    
    
    function updatFriendshipDateForPeopleWidthEngagement(){
        
        $sqlUpdate = "update People as P inner join view_people_memberstate as V on P.Id = V.Id ";
        $sqlSet   = "set P.DateOfFriendshipStart = Now() ";
        $sqlWhere = "where ";
        $sqlWhere.= $this->memberState->getHasEngagement("P");  
        $sqlWhere.="AND MemberStateId in (" . PEOPLE_STATE_MEMBERSHIP_ENDED . ", " . PEOPLE_STATE_FRIEND. ", " . PEOPLE_STATE_FRIENDSHIP_ENDED . ", " . PEOPLE_STATE_ONLY_BAPTIST . ", " . PEOPLE_STATE_REGISTRATED . "); ";
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere, "Org_Version", "Id", $this->id, 'Organisationsversion','Beslutsdatum', null, $this->saronUser);
    }


    function  update(){ // TBD
        $this->checkVersionData();
        $update = "update Org_Version ";
        $set = "SET ";        
        $set.= "decision_date = '" . $this->decision_date .  "', ";        
        $set.= "information = '". $this->information . "', ";        
        $set.= "UpdaterName = '". $this->saronUser->getDisplayName() . "' ";        
        $where = "WHERE id = "  . $this->id;
        $this->db->update($update, $set, $where, "Org_Version", "Id", $this->id, 'Organisationsversion','Beslutsdatum', null, $this->saronUser);        

        $result =  $this->select($this->id, RECORD);
        return $result;
    }
            
    function insert(){
        $this->checkVersionData();
        $this->update_Org();
        $this->updatFriendshipDateForPeopleWidthEngagement();

        $sqlInsert = "INSERT INTO Org_Version (decision_date, information, UpdaterName) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->decision_date . "', ";
        $sqlInsert.= "'" . $this->information . "', ";
        $sqlInsert.= "'" . $this->saronUser->getDisplayName() . "')";
        
        $id = $this->db->insert($sqlInsert, "Org_Version", "Id", $this->id, 'Organisationsversion','Beslutsdatum', null, $this->saronUser);
        $result =  $this->select($id, RECORD);
        
        return $result;
    }
    
}