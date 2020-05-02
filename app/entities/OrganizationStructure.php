<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationStructure extends SuperEntity{
    
    private $id;
    private $unitName;
    private $unitDescription;
    private $parentId;
    private $unitType_FK;
    private $parentUnitId_FK;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->unitName = (String)filter_input(INPUT_POST, "UnitName", FILTER_SANITIZE_STRING);
        $this->unitDescription = (String)filter_input(INPUT_POST, "UnitDescription", FILTER_SANITIZE_STRING);
        $this->unitType_FK = (int)filter_input(INPUT_POST, "UnitType_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->parentUnitId_FK = (int)filter_input(INPUT_POST, "ParentUnitId_FK", FILTER_SANITIZE_NUMBER_INT);
        $this->parentId = (int)filter_input(INPUT_GET, "ParentId", FILTER_SANITIZE_NUMBER_INT);
    }
    
    function select($id = -1, $rec="Records"){
        $select = "Select *, Type.Id as TypeId, Tree.Id as TreeId, (Select count(*) from BusinessUnitTree as bt where bt.ParentUnitId_FK = Tree.Id) as HasSubUnit, ";
        $select.= $this->saronUser->getRoleSql(false) . " ";
        $from = "from BusinessUnitTree as Tree inner join BusinessUnitType as Type on Tree.UnitType_FK = Type.Id ";
        
            if($id < 0){
            if($this->parentId === -1){
                $where = "WHERE ParentUnitId_FK is null ";
            }
            else{
                $where = "WHERE ParentUnitId_FK = " . $this->parentId . " ";                
            }
            $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);    
            return $result;
        }
        else{
            $result = $this->db->select($this->saronUser, $select , $from, "WHERE Tree.Id = " . $id . " ", $this->getSortSql(), $this->getPageSizeSql(), $rec);        
            return $result;
        }
    }

    function insert(){
        $sqlInsert = "INSERT INTO BusinessUnitTree (UnitName, UnitDescription, UnitType_FK, ParentUnitId_FK, Updater) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $this->unitName  . "', ";
        $sqlInsert.= "'" . $this->unitDescription . "', ";
        $sqlInsert.= "'" . $this->unitType_FK . "', ";
        $sqlInsert.= "'" . $this->parentId . "', ";
        $sqlInsert.= "'" . $this->saronUser->ID . "')";
        
        $id = $this->db->insert($sqlInsert, "BusinessUnitTree", "Id");
            return $this->select($id, "Record");
    }
    
    
    function update(){
        $update = "UPDATE BusinessUnitTree ";
        $set = "SET ";        
        $set.= "UnitName='" . $this->unitName . "', ";        
        $set.= "UnitDescription='" . $this->unitDescription . "', ";        
        $set.= "unitType_FK='" . $this->unitType_FK . "', ";        
        $set.= "parentUnitId_FK='" . $this->parentUitId_FK . "', ";        
        $set.= "Updater='" . $this->saronUser->ID . "' ";
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }

    function delete(){
        return $this->db->delete("delete from BusinessUnitTree where Id=" . $this->id);
    }
}
