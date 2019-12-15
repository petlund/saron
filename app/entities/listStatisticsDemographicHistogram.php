<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';



/*** REQUIRE USER AUTHENTICATION ***/
$requireEditorRole = false;
    $saronUser = new SaronUser(wp_get_current_user());    

if(!isPermitted($saronUser, $requireEditorRole)){
    echo notPermittedMessage();
}
else{
    $sqlCount = "select 0 as c"; 

    try{
        $db = new db();
        $sqlSelect = "SELECT Gender, count(*) as amount, ";
        $sqlFrom.= "FROM People ";
        $sqlGroupOrder = "group by ageGroup, Gender order by ageGroup";

        // Members age
        $sqlSelect1= "((EXTRACT(YEAR FROM NOW()) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere1= "WHERE DateOfMembershipStart is not null and DateOfMembershipEnd is null and DateOfDeath is null ";
        $result1 = $db->select($saronUser, $sqlSelect . $sqlSelect1, $sqlFrom, $sqlWhere1, $sqlGroupOrder, "");    

        // Members age when join the Congagregation
        $sqlSelect2 = "((EXTRACT(YEAR FROM DateOfMembershipStart) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere2 = "WHERE DateOfMembershipStart is not null and DateOfMembershipEnd is null and DateOfDeath is null ";
        $sqlWhereLastYears2a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfMembershipStart)) < 5 ";
        $result2 = $db->select($saronUser, $sqlSelect . $sqlSelect2, $sqlFrom, $sqlWhere2, $sqlGroupOrder, "");    
        $result2a = $db->select($saronUser, $sqlSelect . $sqlSelect2, $sqlFrom, $sqlWhere2 . $sqlWhereLastYears2a, $sqlGroupOrder, "");    

        // Members age when leave the Congagregation
        $sqlSelect3= "((EXTRACT(YEAR FROM DateOfMembershipEnd) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere3= "WHERE DateOfMembershipStart is not null and DateOfMembershipEnd is not null  and DateOfDeath is null ";
        $sqlWhereLastYears3a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfMembershipEnd)) < 5 ";
        $result3 = $db->select($saronUser, $sqlSelect . $sqlSelect3, $sqlFrom, $sqlWhere3, $sqlGroupOrder, "");    
        $result3a = $db->select($saronUser, $sqlSelect . $sqlSelect3, $sqlFrom, $sqlWhere3 . $sqlWhereLastYears3a, $sqlGroupOrder, "");    

        // Members age when baptist
        $sqlSelect4= "((EXTRACT(YEAR FROM DateOfBaptism) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere4= "WHERE DateOfDeath is null and DateOfBaptism is not null ";
        $sqlWhereLastYears4a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfBaptism)) < 5 ";
        $result4 = $db->select($saronUser, $sqlSelect . $sqlSelect4, $sqlFrom, $sqlWhere4, $sqlGroupOrder, "");    
    //    $result4a = $db->select($saronUser, $sqlSelect . $sqlSelect4, $sqlFrom, $sqlWhere4 . $sqlWhereLastYears4a, $sqlGroupOrder, "");    

        // Members age when baptist in this congagregation
        $sqlSelect5= "((EXTRACT(YEAR FROM DateOfBaptism) - EXTRACT(YEAR FROM DateOfBirth)) DIV 5) as ageGroup ";
        $sqlWhere5 = "WHERE DateOfDeath is null and DateOfBaptism is not null and CongregationOfBaptismThis=2 ";
        $sqlWhereLastYears5a = " and (EXTRACT(YEAR FROM Now()) - EXTRACT(YEAR FROM DateOfBaptism)) < 5 ";
        $result5 = $db->select($saronUser, $sqlSelect . $sqlSelect5, $sqlFrom, $sqlWhere5, $sqlGroupOrder, "");    
        $result5a = $db->select($saronUser, $sqlSelect . $sqlSelect5, $sqlFrom, $sqlWhere5 . $sqlWhereLastYears5a, $sqlGroupOrder, "");    


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
        $db = null;
    }
    catch(Exception $error){
        echo $error->getMessage();
        $db = null;
    }

}

