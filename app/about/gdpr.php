<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/SaronCookie.php";
require_once SARON_ROOT . "menu.php";
?>

<!DOCTYPE html>
    <html>
        <head>
            <title><?php echo NameOfRegistry;?>  - GDPR</title>
        </head>
        <body>
                Write your own text about GDPR!
        </body>        
    </html>