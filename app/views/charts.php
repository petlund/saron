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
        <title><?php echo NameOfRegistry;?> - Statistik - grafik</title>
   </head>
    <body>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>        
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/charts/chartUtil.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/charts/lineChart.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/charts/histogram.js"></script>     
        <div class="saronSmallText">Grafik</div>
        <table id="StatisticsChart"></table>
        <table id="HistogramChart"></table>
        
    </body>
</html>