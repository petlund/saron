<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "menu.php";


    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Statistik</title>
   </head>
    <body>
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/statistics.js"></script>     
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/localization/jquery.jtable.se.js"></script>                  
        <div class="saronSmallText"></div>
        <div id="STATISTICS"></div> 
    </body>
</html>