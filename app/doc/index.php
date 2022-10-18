<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    require_once "config.php";
    require_once SARON_ROOT . "app/util/AppCanvasName.php";
    require_once SARON_ROOT . "menu.php";
    ?>

    <html>   
        <Head>
            <meta charset="UTF-8">
            <title><?php echo NameOfRegistry;?> - Dokument</title> 
        </Head>
        <body>
            <?php
                echo "<iframe  width='100%' height='1200' src=/" . SARON_URI . "app/util/getPDF.php?AppCanvasName=" . getAppCanvasName("help") . " ";
                echo "title='About'></iframe>";
            ?>
        </body>
    </html> 
    
