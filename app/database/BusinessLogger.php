<?php

require_once SARON_ROOT . 'app/database/db.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';


class BusinessLogger{
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }
    
    
    function createInsertDescription($listRow, $changeType){
        $rowObj = new ArrayObject($listRow[0]);
        
        $description = "";
        $iterator = $rowObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key();
            if(!($this->str_ends_with($key, 'Encrypt') OR $this->str_ends_with($key, 'Hidden'))){
                $value = $iterator->current();
                $description.= "<b>" . $key . ": </b>'" . $value . "'<br> ";
            }
            $iterator->next();
        }
        
        if(strlen($description)>0){
            return "<b>" . $changeType . "</b><br>" . $description;
        }
        return "<b>Ingen " . $changeType . "</b>";
    }
    
    
    
    function createDeleteDescription($listRow, $changeType){
        if(empty($listRow)){
            return "";
        }
        $rowObj = new ArrayObject($listRow[0]);
        
        $description = "<b>" . $changeType . "</b><br>";
        $iterator = $rowObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key(); 
            if(!($this->str_ends_with($key, 'Encrypt') OR $this->str_ends_with($key, 'Hidden'))){
                $value = $iterator->current();
                $description.= "<b>" . $key . ": </b>'" . $value . "'<br> ";
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
            
            if(!($this->str_ends_with($key, 'Encrypt') OR $this->str_ends_with($key, 'Hidden'))){
                if($prevValue !== $postValue){
                    $description.= "<b>" . $key . ": </b>'" . $prevValue . "' <b>==></b> '" . $postValue . "'<br>";
                }
                else{// enable if you want to se all fields
                    //$description.= "<b>" . $key . ": </b>" . $prevValue . "<br>";                
                }
            }
            $iterator->next();
        }
        if(strlen($description)>0){
            return "<b>" . $changeType . "</b><br>" . $description;
        }
        return "<b>Ingen " . $changeType . "</b>";
    }    
    
    
    function getChangeValidationSQL($keyTable, $keyColumn, $key){
        switch ($keyTable){
            case 'Statistics':
                return "Select * from Statistics Where EXTRACT(YEAR FROM year) = " . $key;
            case 'Org_Tree':
                return "Select T1.*, T2.Name as ParentUnitName from Org_Tree as T1 left outer join Org_Tree as T2 on T1.ParentTreeNode_FK=T2.Id Where T1.Id = " . $key;
            case 'People':
                return SQL_STAR_PEOPLE . " From view_people_memberstate as People Where " . $keyColumn . " = " . $key;
            case 'Homes':
                return SQL_STAR_HOMES . " From Homes Where " . $keyColumn . " = " . $key;
            case 'view_organization':
                return 'SELECT *, ' . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_MEMBERSTATENAME_HIDDEN . " as Person From view_organization Where " . $keyColumn . " = " . $key;
            default:
                return "Select * from " . $keyTable . " Where " . $keyColumn . " = " . $key;
        }
    }
    
    
    
    function insertLogPost($keyTable, $keyColumn, $key, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser){
        $sqlInsert = "INSERT INTO Changes (ChangeType, User, BusinessKey, Description, Inserter, InserterName) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $changeType . "', ";
        $sqlInsert.= "'" . $saronUser->userDisplayName . "', ";
        $sqlInsert.= '"' . $businessKeyValue . '", ';
        $sqlInsert.= '"' . $description . '", ';
        $sqlInsert.= $saronUser->WP_ID . ", ";
        $sqlInsert.= "'" . $saronUser->userDisplayName . "')";
        
        $this->db->insert($sqlInsert, 'Changes', 'Id', '',null, '', $saronUser, false);

        $sqlDelete = "DELETE FROM Changes where DATEDIFF(Now(), Inserted) > " . CHANGE_LOG_IN_DAYS;
        $this->db->delete($sqlDelete, 'Changes', 'Id', -1, '', null, '', $saronUser, false);
    }

    
    
    function getBusinessKey($keyTable, $keyColumn, $key, $businessKeyName, $user){
        if(!($key > 0)){
            return '<b>' . $businessKeyName . ':</b> *';            
        }

        switch ($keyTable){
            
            case 'People':
                $sql = 'SELECT ' . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_MEMBERSTATENAME . " as KeyValue From view_people_memberstate as People Where Id = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'Homes':
                $sql =  'SELECT ' . DECRYPTED_FAMILYNAME . " as KeyValue From Homes Where Id = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'News':
                $sql = "SELECT DATE_FORMAT(news_date, " . DATE_FORMAT . ") as KeyValue  From News Where Id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'view_organization':
                $sql = "SELECT PosKeyValue as KeyValue From view_organization Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'MemberState':
                $sql = "SELECT Name as KeyValue From MemberState Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'Org_Role':
                $sql = "SELECT Name as KeyValue From Org_Role Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'Org_Tree':
                $sql = "SELECT Name as KeyValue From Org_Tree Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'Org_UnitType':
                $sql = "SELECT Name as KeyValue From Org_UnitType Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'Org_PosStatus':
                $sql = "SELECT Name as KeyValue From Org_PosStatus Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'Org_Version':
                $sql = "";
                if($key>0){
                    $sql = "SELECT DATE_FORMAT(decision_date, " . DATE_FORMAT . ") as KeyValue From Org_Version Where id  = " . $key;
                }
                else{
                    $sql = "SELECT max(DATE_FORMAT(decision_date, " . DATE_FORMAT . ")) as KeyValue From Org_Version";                    
                }                    
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            
            case 'view_org_role_unittype':   
                $sql = "SELECT Id as KeyValue From view_org_role_unittype Where Id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
        
            case 'SaronUser': 
                $sql = "SELECT UserDisplayName as KeyValue From SaronUser Where " . $keyColumn . " = " . $key;
                return '<b>' . $businessKeyName . ': </b> ' . $this->getBusinessKeyValue($sql);
            
            case 'Statistics':
                $sql = "SELECT year as KeyValue From Statistics Where EXTRACT(YEAR FROM year) = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
                
            default:
                return 'Nyckel ej definerad';
        }
    }
    
    
    private function getBusinessKeyValue($sql){
        $result = $this->db->sqlQuery($sql);
        if($result){
            $resultObj = new ArrayObject($result[0]);
            $businessKey = $resultObj['KeyValue'];
            return $businessKey;
        }
        return 'Saknas';
        
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
