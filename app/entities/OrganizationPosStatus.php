<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationPosStatus extends SuperEntity{
    
    private $name;
    private $description;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->statusfilter = (String)filter_input(INPUT_GET, "statusfilter", FILTER_SANITIZE_STRING);
        $this->name = (String)filter_input(INPUT_POST, "Name", FILTER_SANITIZE_STRING);
        $this->description = (String)filter_input(INPUT_POST, "Description", FILTER_SANITIZE_STRING);
    }

     function select(){
        switch ($this->resultType){
        case OPTIONS:
            return $this->selectOptions();     // vacant is not hanled yet  
        case RECORDS:
            return $this->selectDefault();       
        case RECORD:
            return $this->selectDefault();       
        default:
            return $this->selectDefault();
        }
    }
    
    
    function selectDefault($idFromCreate = -1){
        $id = $this->getId($idFromCreate, $this->id);
        $rec = RECORDS;
        $select = "SELECT *, " . $this->saronUser->getRoleSql(false) . " ";
        $from = "FROM Org_PosStatus ";
        
        if($id > 0){
            $rec = RECORD;
            $where = "WHERE Id = " . $id . " ";
        }
        else{
            $where = "";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
        return $result;
    }

    
    
    function selectOptions(){
        $select = "SELECT Id as Value, Name as DisplayText ";
        $from = "FROM Org_PosStatus ";
        $order = "Order by SortOrder ";
        $where = "";
        
        if($this->tablePath === TABLE_NAME_ENGAGEMENT . "/" . TABLE_NAME_ENGAGEMENTS){
            switch ($this->source){
                case SOURCE_EDIT:            
                    $where.= "WHERE Id not in (5, 6) "; // Tillsätts ej, funktionsorganisation
                    break;
                case SOURCE_CREATE:            
                    $where.= "WHERE Id < 4 "; // Tillsätts ej
                    break;
                default:
                    $where = "";
            }
        }
        
        $result = $this->db->select($this->saronUser, $select , $from, $where, $order, "", OPTIONS);    
        return $result; 
    }
    
    
    
    function update(){
        $update = "UPDATE Org_PosStatus ";
        $set = "SET ";        
//        $set.= "Name='" . $this->name . "', ";        Is not editable
        $set.= "Description='" . $this->description . "', ";        
        $set.= "UpdaterName='" . $this->saronUser->getDisplayName() . "', ";        
        $set.= "Updater='" . $this->saronUser->WP_ID . "' ";
        $where = "WHERE Id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id, RECORD);
    }

    
    
    function delete(){
        return $this->db->delete("delete from Org_UnitType where Id=" . $this->id);
    }
}
