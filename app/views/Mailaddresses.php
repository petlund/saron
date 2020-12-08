<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once 'config.php';
    require_once SARON_ROOT . "app/access/SaronCookie.php";
    require_once SARON_ROOT . "menu.php";
    require_once SARON_ROOT . 'app/database/queries.php';
    require_once SARON_ROOT . 'app/database/db.php';
   
    if(!hasValidSaronSession()){
       exit();
   }
   
    $db = new db();
    $sql = "select ";
    $sql.= DECRYPTED_ALIAS_EMAIL;
    $sql.= " from People ";
    $sql.= "where " . DECRYPTED_EMAIL . " like '%@%' ";
    $sql.= "and DateOfMembershipStart is not null and DateOfMembershipEnd is null and DateOfDeath is null ";
    $sql.= "group by Email ";
    $sql.= "order by Email"; 
?>
<html>   
    <Head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Mailadresser</title> 
    </Head>
    <body>

    <H1>Mailadresser att kopiera och klistra in i adressfält för hemlig kopia.</H1>

    <?php
        $rsRecords = $db->sqlQuery($sql);
        foreach($rsRecords as $record){
            echo $record['Email'] . ", "; 
        } 
    ?>

    </body>
</html>
        

