<?php
require_once SARON_ROOT . 'app/database/db.php'; 
require_once SARON_ROOT . 'app/entities/SuperEntity.php'; 


class Statistics extends SuperEntity{

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
    }
    
    function select(){
        $this->deleteEmptyHomes(); // clean up
        $this->updateStatistics();

        $sqlSelect = "SELECT * , format(average_age, 1) as avg_age, format(average_membership_time, 1) as avg_membership_time, diff ";
        $result = $this->db->select($this->saronUser, $sqlSelect, "From Statistics ", "", $this->getSortSql(),  $this->getPageSizeSql());    
        return $result;
    }

    
    private function updateStatistics(){
        $lastMontForUpdate = 2; //prev year can be updated undtil feb (2)
        $ts = $this->getLatestStatisticTimestamp();
        
        if($ts->latestStatisticTimeStampYear !== $ts->currentYear){
            $this->update($ts->latestStatisticTimeStampYear, $ts->lastTimestampOfTheYear);  // close prev year              
            $this->insert(); // start a new year       
        }
        $ts = $this->getLatestStatisticTimestamp();
        $this->update($ts->latestStatisticTimeStampYear, $ts->currentTimestamp); // update current year
        if(date('n') <= $lastMontForUpdate){
            $this->update($ts->prevStatisticTimeStampYear, $ts->lastTimestampOfPrevYear); // update prev year            
        }
    }
    
    
    private function getLatestStatisticTimestamp(){
        $sqlSelect = "SELECT "
                . "DATE_FORMAT( MAX( Year ),  '%Y' ) AS latestStatisticTimeStampYear, "
                . "year as latestStatisticTimeStamp, "
                . "concat(DATE_FORMAT( MAX( Year ), '%Y' ), '-12-31') as lastTimestampOfTheYear, "
                . "Now() as currentTimestamp, "
                . "DATE_FORMAT(Now(), '%Y') as currentYear, "
                . "concat(DATE_FORMAT( MAX( Year ), '%Y' ) - 1, '-12-31') as lastTimestampOfPrevYear, "
                . "DATE_FORMAT( MAX( Year ),  '%Y' ) - 1 AS prevStatisticTimeStampYear ";
        
        $sqlFrom = "FROM Statistics";

        $jsonLastTimeStamp = $this->db->select($this->saronUser, $sqlSelect, $sqlFrom, "", "", ""); // get last update
        $lastTimeStamp = json_decode($jsonLastTimeStamp);
        return $lastTimeStamp->Records[0];
    }
    
    private function insert(){
        $sqlInsert = "Insert into Statistics values (Now(),0,0,0,0,0,0,0,0)";
        $this->db->insert($sqlInsert, "Statistics", "Year");
    }

    private function update($statisticYear, $statisticTimeStamp){

        $sqlUpdate = "update Statistics ";
        
        $sqlMembersSaldo="(Select count(*) from People where EXTRACT( YEAR FROM DateOfMembershipStart ) <= EXTRACT( YEAR FROM year ) and (EXTRACT( YEAR FROM DateOfMembershipEnd ) > EXTRACT( YEAR FROM year ) or DateOfMembershipEnd is null) and (EXTRACT( YEAR FROM DateOfDeath ) > EXTRACT( YEAR FROM year ) or DateOfDeath is null)) ";
        $sqlNewMemberByYear="(Select count(*) from People where EXTRACT( YEAR FROM DateOfMembershipStart) = EXTRACT( YEAR FROM year))";
        $sqlEndingMembershipByYear="(Select count(*) from People as p where EXTRACT( YEAR FROM DateOfMembershipEnd) = EXTRACT( YEAR FROM year) or EXTRACT( YEAR FROM DateOfDeath) = EXTRACT( YEAR FROM year))";
        $sqlDeadByYear="(Select count(*) from People as p where EXTRACT( YEAR FROM DateOfDeath) = EXTRACT( YEAR FROM year))";
        $sqlBaptistByYear="(Select count(*) from People as p where EXTRACT( YEAR FROM DateOfBaptism) = EXTRACT( YEAR FROM year) and CongregationOfBaptismThis=2)";

        $sqlSet = "set year = '" . $statisticTimeStamp . "', ";
        $sqlSet.= "number_of_members=";
        $sqlSet.= $sqlMembersSaldo;
        $sqlSet.= ", ";
        $sqlSet.= "number_of_new_members=";
        $sqlSet.= $sqlNewMemberByYear;
        $sqlSet.= ", ";
        $sqlSet.= "number_of_baptist_people=";
        $sqlSet.= $sqlBaptistByYear;
        $sqlSet.= ", ";
        $sqlSet.= "number_of_finnished_members=";
        $sqlSet.= $sqlEndingMembershipByYear;    
        $sqlSet.= ", ";
        $sqlSet.= "number_of_dead=";
        $sqlSet.= $sqlDeadByYear;
        $sqlSet.= ", ";
        $sqlSet.= "diff=number_of_new_members-number_of_finnished_members, ";
        $sqlSet.= "average_age=";
        $sqlSet.= "(Select Round(avg(extract(YEAR from Now())-extract(YEAR from DateOfBirth)),1) from People where DateOfDeath is null and DateOfMembershipStart is not null and DateOfMembershipEnd is null) ";
        $sqlSet.= ", ";
        $sqlSet.= "average_membership_time=";
        $sqlSet.= "(Select Round(Avg(extract(YEAR from Now())-extract(YEAR from DateOfMembershipStart)),1) from People where DateOfDeath is null and DateOfMembershipStart is not null and DateOfMembershipEnd is null) "; 

        $sqlWhere = "WHERE EXTRACT(YEAR FROM year)=" . $statisticYear;
        
        $this->db->update($sqlUpdate, $sqlSet, $sqlWhere);
    }
    
    
    private function deleteEmptyHomes(){
        $deleteSql = "delete from Homes where Homes.Id not in (select Homeid from People where HomeId is not null group by HomeId)";
        $this->db->delete($deleteSql);
    }
}