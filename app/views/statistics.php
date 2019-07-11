<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . "menu.php";


    /*** REQUIRE USER AUTHENTICATION ***/
    isLoggedIn();
    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Statistik</title>
   </head>
    <body>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
 
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/statistics.js"></script>     
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery.jtable.min.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/localization/jquery.jtable.se.js"></script>                  
        <div class="saronSmallText"></div>
        <div id="STATISTICS"></div> 
    </body>
</html>