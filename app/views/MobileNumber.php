<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once 'config.php';
    require_once SARON_ROOT . "app/access/wp-authenticate.php";
    require_once SARON_ROOT . "menu.php";
    require_once SARON_ROOT . 'app/database/queries.php';
    require_once SARON_ROOT . 'app/database/db.php';

    isLoggedIn();

    $db = new db();
    $sql ="Select " . DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME_FL . ", ";
    $sql.= DECRYPTED_ALIAS_MOBILE . " FROM People WHERE " . SQL_WHERE_MEMBER . " and " . DECRYPTED_MOBILE . " is not null and "; 
    $sql.= "(Select count(*) from People as p where People.HomeId=p.HomeId and " . DECRYPTED_EMAIL . " like '%@%')  = 0 ";

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
                echo $record['Name_FL'] . ": " . $record['Mobile'] . "<br>"; 
            }

        }
        else{
            echo "Inga mobilnummer returnerades...";
        }
    ?>

    </body>
</html>
        
