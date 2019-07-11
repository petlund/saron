<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once "config.php";
    require_once SARON_ROOT . "app/access/wp-authenticate.php";
    require_once SARON_ROOT . "app/database/ping.php";
   
    
    /*** REQUIRE USER AUTHENTICATION ***/
    isLoggedIn();
    $user = wp_get_current_user();
    
?> 
<!doctype html>
<html>
  <head> 
    <meta charset="UTF-8">
    <?php include ('app/util/js.php') ?>
    <link rel="icon" href=<?php echo Favicon;?> type="png"/>        

    <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-3.3.1.js"></script>
    <script src="/<?php echo SARON_URI;?>app/js/util/menu.js"></script>
    <script src="/<?php echo SARON_URI;?>app/js/util/util.js"></script>
    <link href="/<?php echo SARON_URI;?>jtable/themes/lightcolor/gray/jtable.min.css" rel="stylesheet" type="text/css" />        
    <link href="/<?php echo SARON_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet" type="text/css" />          
    <link rel="stylesheet" type="text/css" href="/<?php echo SARON_URI;?>app/css/saron.css" />

    <body onhashchange="restartTimer()">
        <Table  class='saronSmallText'>
            <Tr>
                <td style='text-align: left'><?php echo ping()?></td><td id="demo"></td>
                <td style='text-align: right'><?php include("app/includes/userDisplay.php")?></td>
            </Tr>
        </Table>
        <!-- Use this navigation div as your menu bar div -->
        <ul id="menu-bar">
            <li>
                <a href="/<?php echo SARON_URI;?>">Hem</a>
            </li> 
            <li>
                <a href="#">Register</a>
                <ul>
                    <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=people">Personuppgifter</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=baptist"> - Dopuppgifter</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=member"> - Medlemsuppgifter</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=keys">[ - Nyckelinnehav Används ej]</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=total">Registeröversikt</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Rapporter</a>
                <ul>
                    <li><a href="/<?php echo SARON_URI;?>app/pdf/DirectoryReport.php" target="_blank">Adresskalender (pdf-fil)</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/pdf/BaptistDirectoryReport.php" target="_blank">Dopregister (pdf-fil)</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/efk.php">EFK-statistik</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=birthdays">Födelsedagslista</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/homes.php">Hem som saknar mailuppgifter</a></li>                    
                    <li><a href="/<?php echo SARON_URI;?>app/views/Mailaddresses.php">Mailadresser</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/statistics.php">Medlemsstatistik</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/charts.php">Medlemsstatistik grafik</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/pdf/DossierReport.php" target="_blank">Godkännande personuppgifter (pdf-fil för alla i registret)</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/views/users.php">Användare</a></li>
                    <li><a href="#">[Adressetiketter (pdf-fil)]</a></li>
                    <ul>
                        <li><a href="/<?php echo SARON_URI;?>app/views/letterLabels.php" target="_blank">Format A</a></li>
                        <li><a href="/<?php echo SARON_URI;?>app/views/letterLabels.php" target="_blank">Format B</a></li>
                        <li><a href="/<?php echo SARON_URI;?>app/views/letterLabels.php" target="_blank">Format C</a></li>
                    </ul>    
                </ul>
            </li>
            <li>
                <a href="">Om <?php echo NameOfRegistry;?></a>
                <ul>
                    <li><a href="/<?php echo SARON_URI;?>app/about/about.php">Om <?php echo NameOfRegistry;?></a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/about/gdpr.php" target="_empty">Hantering av personuppgifter</a></li>
                    <li><a href="/<?php echo SARON_URI;?>app/about/help.php" target="_empty">Hjälp</a></li>
                </ul>
            </li>
            <li>
                <a href="/<?php echo SARON_URI;?>app/access/login.php?logout=true">Logga ut</a>
            </li>
            <li>
                <a href="/<?php echo SARON_URI;?>app/database/extractDB.php">Extract</a>
            </li>
            <li> 
                <a href="/<?php echo SARON_URI;?>app/database/insertDB.php">Insert</a>
            </li>
        </ul>
    </body>
</html>