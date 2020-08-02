<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationUnitMember extends SuperEntity{
    
    private $id;
    private $org_Pos_FK;
    
    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);
        
        $this->id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->org_Pos_FK = (int)filter_input(INPUT_GET, "Org_Pos_FK", FILTER_SANITIZE_NUMBER_INT);
    }


    function select($id = -1, $rec=RECORDS){
        $select = "SELECT * "; 
        $from = "FROM Org_Pos_Tree ";

        if($this->id > 0){
            $where = "";
        }
        else if($this->org_Pos_FK > 0){
            $where = "WHERE Org_Pos_FK = " . $this->org_Pos_FK . " ";
        }
        else{
            $where = "";
        }

        $result = $this->db->select($this->saronUser, $select , $from, $where, $this->getSortSql(), $this->getPageSizeSql(), $rec);        
        return $result;
    }

    function insert(){
        $sqlInsert = "INSERT INTO Org_UnitMember (Org_tree_FK, Org_Pos_FK) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= $this->org_tree_FK . ", ";
        $sqlInsert.= $this->org_Pos_FK . " ";
        
        $id = $this->db->insert($sqlInsert, "Org_UnitMember", "Id");
        return $this->select($id, RECORD);
    }
    
    
    function update(){
        $update = "UPDATE Org_UnitMember ";
        $set = "SET ";        
        $set.= "Org_tree_FK=" . $this->Org_tree_FK . ", ";        
        $set.= "Org_Pos_FK=" . $this->org_Pos_FK . " ";        
        $where = "WHERE id=" . $this->id;
        $this->db->update($update, $set, $where);
        return $this->select($this->id);
    }

    function delete(){
        return $this->db->delete("delete from Org_UnitMemebr where Id=" . $this->id);
    }
}
