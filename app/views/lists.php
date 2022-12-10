<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once 'config.php';
    require_once SARON_ROOT . "menu.php";
    require_once SARON_ROOT . "app/util/AppCanvasName.php";
   
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - email</title>
   </head>
    <body>
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_LISTS, "mobileInsteadOfMail.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_LISTS, "email.js");?>"></script>     
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>                  
        
        <br>
        <div id="<?php echo getAppCanvasName();?>"></div>        
    </body>
</html>

