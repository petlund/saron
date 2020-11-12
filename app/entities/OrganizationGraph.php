<?php
require_once SARON_ROOT . 'app/entities/SuperEntity.php';
require_once SARON_ROOT . 'app/entities/SaronUser.php';

class OrganizationGraph  extends SuperEntity{

    function __construct($db, $saronUser){
        parent::__construct($db, $saronUser);        
    }
    function select(){
            switch ($this->selection){
        case "edges":
            return $this->selectEdges();       
        case "nodes":
            return $this->selectNodes();       
        default:
            return $this->selectNodes();       
        }
    }

    function selectNodes(){
        $sql = "select '0' as id, 'Organisation' as label, 'red' as color ";    
        $sql.= "UNION ";    
        $sql.= "select id, Name as label, 'lightblue' as color  from Org_Tree ";    
        $sql.= "UNION ";    
        $sql.= "select -Org_Pos.id, Name as label, 'lightgreen' as color  from Org_Pos inner join Org_Role on Org_Pos.OrgRole_FK = Org_Role.Id";    
        return $response = $this->db->sqlQuery($sql, NETWORK);
    }
    
    function selectEdges(){
        $sql = "select id as 'from', if(ParentTreeNode_FK is null, 0, ParentTreeNode_FK) as 'to' from Org_Tree ";    
        $sql.= "UNION ";    
        $sql.= "select -id as 'from',  OrgTree_FK as 'to' from Org_Pos "; 
        
        return $response = $this->db->sqlQuery($sql, NETWORK);
    }    
}