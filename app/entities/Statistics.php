<?php
require_once SARON_ROOT . 'app/database/db.php'; 
require_once SARON_ROOT . 'app/entities/SuperEntity.php'; 


class Statistics extends SuperEntity{

    function __construct($db, $saronUser) {
        parent::__construct($db, $saronUser);
    }
    
    function select(){
        switch ($this->selection){
        case "demographicHistogram":
            return $this->selectDemographicHistogram();       
        case "details":
            return $this->selectStatisicsDetails();       
        case "efk":
            return $this->selectEFK();       
        default:
            return $this->selectDefault();
        }
    }    
    

    function selectDefault(){
        $this->updateStatistics();

        $sqlSelect = "SELECT * , format(average_age, 1) as avg_age, format(average_membership_time, 1) as avg_membership_time, diff ";
        $result = $this->db->select($this->saronUser, $sqlSelect, "From Statistics ", "", $this->getSortSql(),  $this->getPageSizeSql());    
        return $result;
    }

    function selectStatisicsDetails(){
        $curYear = (int)filter_input(INPUT_GET, "year", FILTER_SANITIZE_NUMBER_INT);

        $sqlSelect="SELECT " . DECRYPTED_ALIAS_LASTNAME . ", " . DECRYPTED_ALIAS_FIRSTNAME . ", " . DECRYPTED_ALIAS_COMMENT . ", DateOfBirth, ";
        $sql =$sqlSelect;  
        $sql.="DateOfMembershipStart as 'event_date', 
            'Ny' as event_type, 1 as 'Diff' 
            FROM `People` as p  
            WHERE extract(YEAR from DateOfMembershipStart)=" . $curYear;
        $sql.=" UNION ";
        $sql.=$sqlSelect;  
        $sql.="DateOfMembershipEnd as 'event_date', 
            'Avslutad' as event_type, -1 as 'Diff' 
            FROM `People` as p  
            WHERE DateOfDeath is null and DateOfMembershipStart is not null and extract(YEAR from DateOfMembershipEnd)=" . $curYear;   
        $sql.=" UNION ";
        $sql.=$sqlSelect;  
        $sql.="DateOfBaptism as 'event_date', 'Döpt' as event_type, 0 as 'Diff' 
            FROM `People` as p  
            WHERE CongregationOfBaptismThis=2 and extract(YEAR from DateOfBaptism)=" . $curYear; 
        $sql.=" UNION ";
        $sql.=$sqlSelect;  
        $sql.="DateOfDeath as 'event_date', 'Avliden' as event_type, -1 as 'Diff' 
            FROM `People` as p  
            WHERE extract(YEAR from DateOfDeath)=" . $curYear . " ";  
        $sql.= $this->getSortSql();
        $sql.= $this->getPageSizeSql();

        $sqlCount  = "select ";        
        $sqlCount .= "(SELECT count(*) FROM `People` as p1 WHERE extract(YEAR from p1.DateOfMembershipStart)=" . $curYear . ") + ";
        $sqlCount .= "(SELECT count(*) FROM `People` as p2 WHERE extract(YEAR from p2.DateOfMembershipEnd)=" . $curYear . ") + ";
        $sqlCount .= "(SELECT count(*) FROM `People` as p4 WHERE extract(YEAR from p4.DateOfDeath)=" . $curYear . ") + ";
        $sqlCount .= "(SELECT count(*) FROM `People` as p3 WHERE p3.CongregationOfBaptismThis=2 and extract(YEAR from p3.DateOfBaptism)=" . $curYear . ") "; 
        $sqlCount .= "as c;";

        $result = $this->db->selectSeparate($this->saronUser, $sql, $sqlCount);    
        return $result;
        
    }

    function selectEFK(){
        $interval = 10;
        $intervals = 11;
        
        $sql = "(";
        for($i = 0; $i<$intervals; $i++){
            $sql.= $this->getIntervalSql($i * $interval + 1, ($i + 1) * $interval);
            $sql.= ") union (";
        }
        
        $sql.= $this->getIntervalSql(null, null);
        $sql.= ") ";

        $sql.= $this->getSortSql();
        $sql.= $this->getPageSizeSql();

        $sqlCount = "select " . ($interval + 1) . " as c"; 

        $result = $this->db->selectSeparate($this->saronUser, $sql, $sqlCount);    
        return $result;
    }
    
    
    private function getIntervalSql($minAge, $maxAge){
        $sqlInterval="";
        $ageAlias = " as AgeInterval, ";
        $sqlWhereInterval = "and extract(year from now())-extract(year from DateOfBirth) "; 

        if($minAge < 100){
            $empty = " ";
        }
        else{
            $empty = "";
        }
            
            
        if($minAge === null and $maxAge===null){
            $sqlLabel="Select 'Totalt' " . $ageAlias;
            $sqlInterval = $sqlWhereInterval . ">0";
        }   
        else{
            if($maxAge!==null){
                $sqlLabel="Select '" . $empty . $minAge . "-" . $maxAge ."'" . $ageAlias;        
                $sqlInterval = $sqlWhereInterval . "between ".  $minAge . " and " . $maxAge;
            }
            else{
                $sqlLabel="Select '" . $empty . $minAge . "-...'" . $ageAlias;        
                $sqlInterval = $sqlWhereInterval . ">= " . $minAge;
            }
        }
        $sqlCount = "count(*) as Amount from People where ";
        $sqlCount.= "extract(year from now()) > extract(year from DateOfMembershipStart) and ";
        $sqlCount.= "(extract(year from now()) = extract(year from DateOfMembershipEnd) or DateOfMembershipEnd is null)"; 

        $sql = $sqlLabel . $sqlCount . $sqlInterval;
        return $sql;
    }
    
    
    function selectDemographicHistogram(){

        $sqlSelect = "SELECT Gender, count(*) as amount, ";
        $sqlFrom.= "FROM People ";
        $sqlGroupOrder = "group by ageGroup, Gender order by ageGroup";

        // Members age
        $sqlSelect1= "((EXTRACT(YEAR FROM NOW()) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere1= "WHERE DateOfMembershipStart is not null and DateOfMembershipEnd is null and DateOfDeath is null ";
        $result1 = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect1, $sqlFrom, $sqlWhere1, $sqlGroupOrder, "");    

        // Members age when join the Congagregation
        $sqlSelect2 = "((EXTRACT(YEAR FROM DateOfMembershipStart) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere2 = "WHERE DateOfMembershipStart is not null and DateOfMembershipEnd is null and DateOfDeath is null ";
        $sqlWhereLastYears2a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfMembershipStart)) < 5 ";
        $result2 = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect2, $sqlFrom, $sqlWhere2, $sqlGroupOrder, "");    
        $result2a = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect2, $sqlFrom, $sqlWhere2 . $sqlWhereLastYears2a, $sqlGroupOrder, "");    

        // Members age when leave the Congagregation
        $sqlSelect3= "((EXTRACT(YEAR FROM DateOfMembershipEnd) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere3= "WHERE DateOfMembershipStart is not null and DateOfMembershipEnd is not null  and DateOfDeath is null ";
        $sqlWhereLastYears3a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfMembershipEnd)) < 5 ";
        $result3 = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect3, $sqlFrom, $sqlWhere3, $sqlGroupOrder, "");    
        $result3a = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect3, $sqlFrom, $sqlWhere3 . $sqlWhereLastYears3a, $sqlGroupOrder, "");    

        // Members age when baptist
        $sqlSelect4= "((EXTRACT(YEAR FROM DateOfBaptism) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere4= "WHERE DateOfDeath is null and DateOfBaptism is not null ";
    //    $sqlWhereLastYears4a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfBaptism)) < 5 ";
        $result4 = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect4, $sqlFrom, $sqlWhere4, $sqlGroupOrder, "");    
    //    $result4a = $db->select($saronUser, $sqlSelect . $sqlSelect4, $sqlFrom, $sqlWhere4 . $sqlWhereLastYears4a, $sqlGroupOrder, "");    

        // Members age when baptist in this congagregation
        $sqlSelect5= "((EXTRACT(YEAR FROM DateOfBaptism) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere5 = "WHERE DateOfDeath is null and DateOfBaptism is not null and CongregationOfBaptismThis=2 ";
        $sqlWhereLastYears5a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfBaptism)) < 5 ";
        $result5 = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect5, $sqlFrom, $sqlWhere5, $sqlGroupOrder, "");    
        $result5a = $this->db->select($this->saronUser, $sqlSelect . $sqlSelect5, $sqlFrom, $sqlWhere5 . $sqlWhereLastYears5a, $sqlGroupOrder, "");    


        $results = '{"Results":['; 
        $results.=$result1 . ', ';
        $results.=$result2 . ', ';
        $results.=$result2a . ', ';
        $results.=$result3 . ', ';
        $results.=$result3a . ', ';
        $results.=$result4 . ', ';
        //$results.=$result4a . ', ';
        $results.=$result5 . ', ';  
        $results.=$result5a;
        $results.=']}';
        echo $results;
        
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

}