<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    require_once "config.php";
    require_once SARON_ROOT . "app/access/wp-authenticate.php";
    require_once SARON_ROOT . "menu.php";
?>
<html>   
    <Head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Välkommen</title> 
    </Head>
    <body> 
        <div class='saronSmallText'>Medlemsregister för församlingen i <?php echo ShortNameOfCongregation;
        echo '<br>Version 2.2.2_RC2';
        echo '<br><br><a href="mailto:' . EmailSysAdmin . '">' . EmailSysAdmin . '</a>';
        ?></div>
    </body>
</html> 
