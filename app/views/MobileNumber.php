<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once "../access/wp-authenticate.php";
    require_once "../../menu.php";
    require_once '../../config/config.php';
    require_once '../database/queries.php';
    require_once '../database/db.php';

    isLoggedIn();

    $db = new db();
    $sql ="Select " . DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME . ", ";
    $sql.= DECRYPTED_ALIAS_MOBILE . " FROM PEOPLE WHERE " . SQL_WHERE_MEMBER . " and " . DECRYPTED_MOBILE . " is not null and "; 
    $sql.= "(Select count(*) from People as p where people.HomeId=p.HomeId and " . DECRYPTED_EMAIL . " like '%@%')  = 0 ";

?>
<html>   
    <Head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Mobilnummer</title> 
    </Head>
    <body>

    <H1>Mobilnummer till hem utan mailadress</H1>

    <?php
        $rsRecords = $db->sqlQuery($sql);
        if($rsRecords !== null){
            foreach($rsRecords as $record){
                echo $record['Mobile'] . ", "; 
            }
            echo "<h1>Samma nummer med namn</h1>";
            foreach($rsRecords as $record){
                echo $record['Name'] . ": " . $record['Mobile'] . "<br>"; 
            }

        }
        else{
            echo "Inga mobilnummer returnerades...";
        }
    ?>

    </body>
</html>
        
