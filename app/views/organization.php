<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once "config.php";
    require_once SARON_ROOT . "menu.php";
    require_once SARON_ROOT . "app/util/AppCanvasName.php";    
?>
<html>   
    <Head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Organisation</title> 
    </Head>
    <body> 
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "unittype.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "unit.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "tree.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "list.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "role.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "role_unittype.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "status.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "pos.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "version.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "memberstateAndReports.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "memberstate.js");?>"></script>     

        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>   
        <div class='saronSmallText'></div>
        <div id="<?php echo getAppCanvasName();?>"></div>        
    </body
</html> 