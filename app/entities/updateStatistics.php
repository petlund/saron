<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class updateStatistics{
    //To Do
    function deleteEmptyHomes(){
        $db = new db();
        $deleteSql = "delete from Homes where Homes.Id not in (select Homeid from People where HomeId is not null)";
        if(!$db->delete($deleteSql)){
            echo "Kunde inte köra städskript som tar bort tomma hem och uppdaterar statistik. Kontakta administratören";
            return;
        }

    }

    function addNewYearIfNeeded(){
        $db = new db();
        $sql="SELECT DATE_FORMAT( MAX( YEAR ),  '%Y' ) AS YEAR FROM Statistics";
        $lastUpdatePrevYear="update Statistics set year = '" . intval(date('Y', time())-1) . "-12-31' where EXTRACT( YEAR FROM year)=" . intval(date('Y', time())-1);
        $insert="Insert into Statistics values ('" . date('Y-m-d', time()) . "',0,0,0,0,0,0,0,0)";

        $listResult = $db->sqlQuery($sql);
        if(!$listResult){
            return;
        }    
        $aRow = mysqli_fetch_array($listResult);
        if($aRow['YEAR']!=intval(date("Y", time()))){
            $rsUpdate = $db->sqlQuery($lastUpdatePrevYear);        
            if(!$rsUpdate){
                return;
            }    
            $rsInsert = $db->sqlQuery($insert);        
            if(!$rsInsert){
                return;
            }    
            return true;        
        }
        else{
            return false;
        }
    }

    function updateCurrentYear(){
        $db = new db();
        $sqlUpdate = "update Statistics ";
        $sqlSet = "set year = now() ";
        $sqlWhere = "WHERE EXTRACT( YEAR FROM year)=" . intval(date('Y', time()));
        if(!$db->update($sqlUpdate, $sqlSet, $sqlWhere)){
            return;
        }

        $sqlMembersSaldo="(Select count(*) from People where EXTRACT( YEAR FROM DateOfMembershipStart ) <= EXTRACT( YEAR FROM year ) and (EXTRACT( YEAR FROM DateOfMembershipEnd ) > EXTRACT( YEAR FROM year ) or DateOfMembershipEnd is null) and (EXTRACT( YEAR FROM DateOfDeath ) > EXTRACT( YEAR FROM year ) or DateOfDeath is null)) ";
        $sqlNewMemberByYear="(Select count(*) from People where EXTRACT( YEAR FROM DateOfMembershipStart) = EXTRACT( YEAR FROM year))";
        $sqlEndingMembershipByYear="(Select count(*) from People as p where EXTRACT( YEAR FROM DateOfMembershipEnd) = EXTRACT( YEAR FROM year) or EXTRACT( YEAR FROM DateOfDeath) = EXTRACT( YEAR FROM year))";
        $sqlDeadByYear="(Select count(*) from People as p where EXTRACT( YEAR FROM DateOfDeath) = EXTRACT( YEAR FROM year))";
        $sqlBaptistByYear="(Select count(*) from People as p where EXTRACT( YEAR FROM DateOfBaptism) = EXTRACT( YEAR FROM year) and CongregationOfBaptismThis=2)";


        $sql="update Statistics set ";
        $sql.="number_of_members=";
        $sql.=$sqlMembersSaldo;
        $sql.=", ";
        $sql.="number_of_new_members=";
        $sql.=$sqlNewMemberByYear;
        $sql.=", ";
        $sql.="number_of_baptist_people=";
        $sql.=$sqlBaptistByYear;
        $sql.=", ";
        $sql.="number_of_finnished_members=";
        $sql.=$sqlEndingMembershipByYear;    
        $sql.=", ";
        $sql.="number_of_dead=";
        $sql.=$sqlDeadByYear;
        $sql.=", ";
        $sql.="diff=number_of_new_members-number_of_finnished_members, ";
        $sql.="average_age=";
    //    $sql.="(Select round(avg(extract(year from year)-extract(year from DateOfBirth)),1) from People as p where per_DateEntered >= EXTRACT( YEAR FROM year ) and (DateOfMembershipEnd < EXTRACT( YEAR FROM year ) or DateOfMembershipEnd is null) and (DateOfDeath < EXTRACT( YEAR FROM year ) or DateOfDeath is null)) ";
        $sql.="(Select Round(avg(extract(YEAR from Now())-extract(YEAR from DateOfBirth)),1) from People where DateOfDeath is null and DateOfMembershipStart is not null and DateOfMembershipEnd is null) ";
        $sql.=", ";
        $sql.="average_membership_time=";
    //    $sql.="(Select round(avg(extract(year from year)-extract(year from DateOfMembershipStart)),1) from People as p where per_DateEntered is not null and (DateOfMembershipEnd < EXTRACT( YEAR FROM year ) or DateOfMembershipEnd is null) and (DateOfDeath < EXTRACT( YEAR FROM year ) or DateOfDeath is null))";
        $sql.="(Select Round(Avg(extract(YEAR from Now())-extract(YEAR from DateOfMembershipStart)),1) from People where DateOfDeath is null and DateOfMembershipStart is not null and DateOfMembershipEnd is null) "; 
        $sql.="where ";
        $sql.="extract(year from year)=extract(year from now()) ";
        $sql.="or extract(year from year)>=extract(year from now())-1 and extract(month from now())=1 ";

        //$sql.="(Round(Avg(extract(YEAR from DateOfMembershipStart)-extract(YEAR from DateOfBirth)),1) as MembStartAge ";    

        if(!$db->sqlQuery($sql)){
            return;
        }
        return;

    }
}