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
        <title><?php echo NameOfRegistry;?> - Statistik - grafik</title>
   </head>
    <body>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>        
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_CHARTS, "charts.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_CHARTS, "timeseries.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_CHARTS, "histogram.js");?>"></script>     
        <div class="saronSmallText">Grafik</div>
        <div id="StatisticsChart"></div>
        <div id="HistogramChart"></div>
        
    </body>
</html>