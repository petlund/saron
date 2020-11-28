<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/cookie.php";
require_once SARON_ROOT . "menu.php";

    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Adressetiketter</title>
    </head>
    <body>
        <H5>Inte implementerat än ...</H5>

    </body>
</html>