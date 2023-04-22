<?php

require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';
define("EMPTY_FIELD", "[TOM]");
define("DIFF_ARROW", "<b> ==> </b>");

class BusinessLogger  extends SuperEntity{
    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
    }
    
    
    function createInsertDescription($listRow, $changeType){
        $rowObj = new ArrayObject($listRow[0]);
        
        $description = "";
        $iterator = $rowObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key();
            if($this->isKeyValid($key)){
                $value = $iterator->current();
                if(strlen($value) === 0){
                    $description.= "<b>" . $key . ": </b>" . EMPTY_FIELD . "<br> ";                    
                }
                else{
                    $description.= "<b>" . $key . ": </b>" . $value . "<br> ";
                }
            }
            $iterator->next();
        }
        
        if(strlen($description)>0){
            return $description;
        }
        return "<b>Ingen " . strtolower($changeType) . "</b>";
    }
    
    
    
    function createDeleteDescription($listRow, $changeType){
        if(empty($listRow)){
            return "";
        }
        $rowObj = new ArrayObject($listRow[0]);
        
        $description = "";
        $iterator = $rowObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key(); 
            if($this->isKeyValid($key)){
                $value = $iterator->current();
                if(strlen($value) === 0){
                    $description.= "<b>" . $key . ": </b>" . EMPTY_FIELD . "<br> ";                    
                }
                else{
                    $description.= "<b>" . $key . ": </b>" . $value . "<br> ";
                }
            }
            $iterator->next();
        }
        
        return $description;
        }
    
    
    
    function createUpdateDescription($prevListRow, $postListRow, $changeType){
        $prevObj = new ArrayObject($prevListRow[0]);
        $postObj = new ArrayObject($postListRow[0]);
        
        $description = "";
        $iterator = $prevObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key(); 
            $prevValue = $iterator->current();
            $postValue = $postObj[$key];
            
            if($this->isKeyValid($key)){
                if($prevValue !== $postValue){
                    if(strlen($prevValue) === 0 AND strlen($postValue) > 0){
                        $description.= "<b>" . $key . ": </b>" . EMPTY_FIELD . DIFF_ARROW . $postValue . "<br>";
                    }
                    if(strlen($prevValue) > 0 AND strlen($postValue) === 0){
                        $description.= "<b>" . $key . ": </b>" . $prevValue . DIFF_ARROW . EMPTY_FIELD . "<br>";
                    }
                    if(strlen($prevValue) > 0 AND strlen($postValue) > 0){
                        $description.= "<b>" . $key . ": </b>" . $prevValue . DIFF_ARROW . $postValue . "<br>";
                    }
                }
            }
            $iterator->next();
        }
        if(strlen($description)>0){
            return $description;
        }
        return "<b>Ingen " . $changeType . "</b>";
    }    
    
    
    function isKeyValid($key){
        if($this->str_ends_with($key, 'Encrypt')){
            return false;
        }
        if($this->str_ends_with($key, 'Hidden')){
            return false;
        }
        if($key === "Id"){
            return false;
        }
        if($key === "Updater"){
            return false;
        }
        if($key === "UpdaterName"){
            return false;
        }
        if($key === "HomeId"){
            return false;
        }
        if($key === "OldHomeId"){
            return false;
        }
        if($key === "severity"){
            return false;
        }
        if($key === "RoleType"){
            return false;
        }
        if($key === "ParentTreeNode_FK"){
            return false;
        }
        if($key === "OrgUnitType_FK"){
            return false;
        }
        if($key === "MemberStateId"){
            return false;
        }
        if($key === "KeyToChurch"){
            return false;
        }
        if($key === "KeyToExp"){
            return false;
        }
        if($key === "CongregationOfBaptismThis"){
            return false;
        }
        if($key === "Gender"){
            return false;
        }
        if($key === "Inserter"){
            return false;
        }
        if($key === "VisibleInCalendar"){
            return false;
        }
        if($key === "PosEnabled"){
            return false;
        }
        if($key === "SubUnitEnabled"){
            return false;
        }
        if($key === "Letter"){
            return false;
        }
        if($key === "Inserter"){
            return false;
        }
        if($key === "Updater"){
            return false;
        }
        return true;
    }
    
    
    function getChangeValidationSQL($keyTable, $keyColumn, $key){
        switch ($keyTable){
            case 'News':
                return "Select * from view_news Where Id = " . $key;
            case 'Statistics':
                return "Select * from Statistics Where EXTRACT(YEAR FROM year) = " . $key;
            case 'Org_Tree':
                return "SELECT * From view_organization_tree Where " . $keyColumn . " = " . $key;
            case 'Org_Role':
                return "SELECT * From view_role Where " . $keyColumn . " = " . $key;
            case 'Org_UnitType':
                return "SELECT * From view_unittype Where " . $keyColumn . " = " . $key;
            case 'People':
                return SELECT_ALL_FIELDS_FROM_VIEW_PEOPLE . " From view_people as People Where " . $keyColumn . " = " . $key;
            case 'Homes':
                return SQL_STAR_HOMES . " From Homes Where " . $keyColumn . " = " . $key;
            case 'Org_Pos':
                return 'SELECT *, ' . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_MEMBERSTATENAME_HIDDEN . " as Person From view_organization_pos Where " . $keyColumn . " = " . $key;
            default:
                return "Select * from " . $keyTable . " Where " . $keyColumn . " = " . $key;
        }
    }
    
    
    
    function insertLogPost($keyTable, $keyColumn, $key, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser, $wpUser=null){
        If(strlen($businessKeyValue) === 0){
            If($keyTable === "SaronUser"){ //When the application has been in standby longer than the ticket session time
                if($wpUser !== null){
                    $businessKeyValue = $wpUser->user_login;
                }
                else{
                    $businessKeyValue = "Saknad!";                
                }
            }
            else{
                $businessKeyValue = "Saknad!";
            }
        }

        $formattedBusinessKeyValue = '<b>' . $businessKeyName . ':</b> ' . $businessKeyValue;

        $sqlInsert = "INSERT INTO Changes (ChangeType, User, BusinessKeyEncrypt, DescriptionEncrypt, Inserter, InserterName) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'<b>" . $changeType . "</b>', ";
        $sqlInsert.= "'<b>" . $saronUser->getUserName() . "</b>', ";
        $sqlInsert.= $this->getEncryptedSqlString($formattedBusinessKeyValue) . ', ';
        $sqlInsert.= $this->getEncryptedSqlString($description) . ', ';
        $sqlInsert.= $saronUser->getWP_ID() . ", ";
        $sqlInsert.= "'" . $saronUser->getDisplayName() . "')";
        
        $this->db->insert($sqlInsert, 'Changes', 'Id', '',null, '', $saronUser, false);

        $sqlDelete = "DELETE FROM Changes where DATEDIFF(Now(), Inserted) > " . CHANGE_LOG_IN_DAYS;
        $this->db->delete($sqlDelete, 'Changes', 'Id', -1, '', null, '', $saronUser, false);
    }

    
    
    function getBusinessKey($keyTable, $keyColumn, $key){
            if(!($key > 0)){
            return '*';            
        }

        switch ($keyTable){
            
            case 'People':
                $sql = 'SELECT ' . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_MEMBERSTATENAME . " as KeyValue From view_people as People Where Id = " . $key;
                break;
            case 'Homes':
                $sql =  'SELECT ' . DECRYPTED_FAMILYNAME . " as KeyValue From Homes Where Id = " . $key;
            break;     
            case 'News':
                $sql = "SELECT DATE_FORMAT(news_date, " . DATE_FORMAT . ") as KeyValue From view_news Where Id  = " . $key;
            break;     
            case 'Org_Pos':
                $sql = "SELECT PosKeyValue as KeyValue From view_organization_pos Where id  = " . $key;
            break;     
            case 'MemberState':
                $sql = "SELECT Name as KeyValue From MemberState Where id  = " . $key;
            break;     
            case 'Org_Role':
                $sql = "SELECT Name as KeyValue From view_role Where id  = " . $key;
            break;     
            case 'Org_Tree':
                $sql = "SELECT LongName as KeyValue From view_organization_tree Where id  = " . $key;
            break;     
            case 'Org_UnitType':
                $sql = "SELECT Name as KeyValue From Org_UnitType Where id  = " . $key;
            break;     
            case 'Org_PosStatus':
                $sql = "SELECT Name as KeyValue From Org_PosStatus Where id  = " . $key;
            break;     
            case 'Org_Version':
                $sql = "";
                if($key>0){
                    $sql = "SELECT DATE_FORMAT(decision_date, " . DATE_FORMAT . ") as KeyValue From Org_Version Where id  = " . $key;
                }
                else{
                    $sql = "SELECT max(DATE_FORMAT(decision_date, " . DATE_FORMAT . ")) as KeyValue From Org_Version";                    
                }                    
            break;     
            case 'view_org_role_unittype':   
                $sql = "SELECT Id as KeyValue From view_org_role_unittype Where Id  = " . $key;
            break;     
            case 'SaronUser': 
//                $sql = "SELECT UserDisplayName as KeyValue From SaronUser Where " . $keyColumn . " = " . $key;
                $sql = "SELECT UserName as KeyValue From SaronUser Where " . $keyColumn . " = " . $key;
            break;     
            case 'Statistics':
                $sql = "SELECT year as KeyValue From Statistics Where EXTRACT(YEAR FROM year) = " . $key;
            break;     
            default:
                return 'Nyckeltabell ej definerad';
        }
        return $this->getBusinessKeyValue($sql);
    }
    
    
    private function getBusinessKeyValue($sql){
        $result = $this->db->sqlQuery($sql);
        if($result){
            $resultObj = new ArrayObject($result[0]);
            $businessKey = $resultObj['KeyValue'];
            return $businessKey;
        }
        return '';        
    }
    
    
    
    private function str_ends_with($haystack, $needle){
        $haystack_len = strlen($haystack);
        $needle_len = strlen($needle);
        
        if($haystack_len === 0){
            return false;
        }
            
        if($needle_len  === 0){
            return false;
        }
            
        if($needle_len  > $haystack_len){
            return false;
        }
        $haystack_suffix = substr($haystack, $haystack_len - $needle_len);
        if($haystack_suffix === $needle){
            return true;
        }
        return false;
    }
}
