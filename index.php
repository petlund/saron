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
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "news.js");?>"></script>     
        <script type="text/javascript" src="/<?php echo SARON_URI;?>3pp/jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>3pp/jtable/localization/jquery.jtable.se.js"></script>   
        <div class='saronSmallText'>Medlemsregister för församlingen i <?php echo ShortNameOfCongregation;?></div>
        <div id="NEWS"></div> 
    </body>
</html> 