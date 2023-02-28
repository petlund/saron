<?php
require_once "db.php";
require_once "queries.php";
require_once "config.php";
    require_once SARON_ROOT . "menu.php";

define("SALT_LENGTH", 12); // FROM CONFIG

$requireEditorRole = true;
    $saronUser = new SaronUser(wp_get_current_user());    

if(!isPermitted($saronUser, $requireEditorRole, false)){
    echo notPermittedMessage();
    exit();
}
    
echo "<h1>// db open</h1>";
$db = new db();
if($db!==null){
    echo "// NOT NULL <br>";
}

echo "// extract people<br>";

$listResult1 = $db->sqlQuery(SQL_STAR_PEOPLE . " from People");
    $listRowsP = array();
    $pCols = 27;
    $colNames = "(Id, FirstNameEncrypt, LastNameEncrypt, DateOfBirth, DateOfDeath, PreviousCongregation, MembershipNo, VisibleInCalendar, DateOfMembershipStart, DateOfMembershipEnd, NextCongregation, DateOfBaptism, BaptisterEncrypt, "
            . "CongregationOfBaptism, CongregationOfBaptismThis, Gender, EmailEncrypt, MobileEncrypt, KeyToChurch, KeyToExp, CommentEncrypt, HomeId, Updater, Updated, Inserter, Inserted, CommentKeyEncrypt"
            . ") ";
    $cases = array(0,1,1,2,2,3,0,0,2,2,3,2,1,3,0,0,1,1,0,0,1,0,0,2,0,2,1,2,0,3,2,); 
    echo "\$db->delete(\"delete from People\");<br>";

    while($listRowP = mysqli_fetch_array($listResult1)){
        $sql="";
        echo "\$db->insert(\"INSERT INTO People " . $colNames . " VALUES (";
        for($i=0; $i<$pCols; $i++ ){
            if(strlen($listRowP[$i])===0){
                $sql.= "null";
            }
            else{
                switch($cases[$i]){ 
                    case 1: //text
                      $sql.= "AES_ENCRYPT('" . salt2() . $listRowP[$i] . "', \" . PKEY . \")";
                      //$sql.= "'" . $listRowP[$i] . "'";
                        break;
                    case 2: //date                    
                        $sql.= "'" . $listRowP[$i] . "'";
                        break;
                    case 3: //text
                        $sql.= "'" . $listRowP[$i] . "'";
                        break;
                    default : //int
                        $sql.= $listRowP[$i];
                }
            }
            if($i < $pCols-1){
                $sql.= ", ";
            }
            else{
                $sql.= ")\", 'People', 'Id');<br>";                
            }
        }
                        
        echo $sql;
        $ok = "<code>echo \"OK ";
        for($j=0; $j<4; $j++){
            $ok.= $listRowP[$j];
            if($j < 3){
                $ok.= ", ";
            }
            else{
                $ok.= "&lt;BR&gt\";</code><br><br>";                
            }
        }
        echo $ok;

    } 
    mysqli_free_result($listResult1);

    echo "<h1>// extract homes</h1>";

$listResult2 = $db->sqlQuery(SQL_STAR_HOMES . " from Homes");
    $pCols = 9;
    $colNames = "(Id, FamilyNameEncrypt, AddressEncrypt, CoEncrypt, City, Zip, Country, PhoneEncrypt, Letter, "
            . "Updated, Updater, UpdaterName. Inserted, Inserter, InserterName) ";
    $cases = array(0,1,1,1,3,3,3,1,0,2,0,2,2,0,2); 
    echo "\$db->delete(\"delete from Homes\");<br>";
    while($listRow2 = mysqli_fetch_array($listResult2)){
        $sql="";
        echo "\$db->insert(\"INSERT INTO Homes " . $colNames . " VALUES (";
        for($i=0; $i<$pCols; $i++ ){
            if(strlen($listRow2[$i])===0){
                $sql.= "null";
            }
            else{
                switch($cases[$i]){ 
                    case 1: //text
                      $sql.= "AES_ENCRYPT('" . salt2() . $listRow2[$i] . "', \" . PKEY . \")";
                      //$sql.= "'" . $listRow2[$i] . "'";
                        break;
                    case 2: //date                    
                        $sql.= "'" . $listRow2[$i] . "'";
                        break;
                    case 3: //text
                        $sql.= "'" . $listRow2[$i] . "'";
                        break;
                    default : //int
                        $sql.= $listRow2[$i];
                }
            }
            if($i < $pCols-1){
                $sql.= ", ";
            }
            else{
                $sql.= ")\", 'Homes', 'Id');<br>";                
            }
        }
                        
        echo $sql;
        $ok = "<code>echo \"OK ";
        for($j=0; $j<4; $j++){
            $ok.= $listRow2[$j];
            if($j < 3){
                $ok.= ", ";
            }
            else{
                $ok.= "&lt;BR&gt\";</code><br><br>";                
            }
        }
        echo $ok;

    } 
    mysqli_free_result($listResult2);
        
echo "<h1>//end</h1>";

try{
    $db=null;
}
catch(Exception $error){
    echo $error;    
    $db=null;
}


    function salt2(){        
        //$abc = "!#$%&()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~";
        $abc = "!#%&()*+,-./0123456789:;=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ^_`abcdefghijklmnopqrstuvwxyz{|}~";
        $str = "";
        
        while(strlen($str)<SALT_LENGTH){
            $str.= substr($abc, rand(0, strlen($abc)), 1);
        }
        return $str;
    }
