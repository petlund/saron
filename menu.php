<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once "config.php";
    require_once SARON_ROOT . "app/access/wp-authenticate.php";
    require_once SARON_ROOT . "app/database/ping.php";
   
 
    /*** REQUIRE USER AUTHENTICATION ***/
    isLoggedIn();
        $saronUser = new SaronUser(wp_get_current_user());
    
?> 
<!doctype html>
<html>
  <head> 
    <meta charset="UTF-8">
<!--        <meta name="viewport" content="width=device-width, initial-scale=1.0">
-->    
    <?php include ('app/util/js.php') ?>
    <link rel="icon" href=<?php echo Favicon;?> type="png"/>        

    <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-3.3.1.js"></script>
    <script src="/<?php echo SARON_URI;?>app/js/util/menu.js"></script>
    <script src="/<?php echo SARON_URI;?>app/js/util/timeout.js"></script>
    <script src="/<?php echo SARON_URI;?>app/js/util/util.js"></script>
    <link href="/<?php echo SARON_URI;?>jtable/themes/lightcolor/gray/jtable.min.css" rel="stylesheet" type="text/css" />        
    <link href="/<?php echo SARON_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet" type="text/css" />          
    <link rel="stylesheet" type="text/css" href="/<?php echo SARON_URI;?>app/css/saron.css" />

    <body >
        <table  class='saronMenuTable saronSmallText'>
            <tr>
                <td style='text-align: left'><?php echo ping()?>
                </td>
                <td>
                </td>
                <td style='text-align: right'><?php include("app/includes/userDisplay.php")?>
                </td>
                <td style='text-align: right'>
                    <div id="timerProgress">
                    <div id="timerBar"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4"><div>
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
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=keys"> - Nyckelinnehav</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/homes.php?tableview=homes">Hem</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=total">Registeröversikt</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Rapporter</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/DirectoryReport.php" target="_blank">Adresskalender (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/BaptistDirectoryReport.php" target="_blank">Dopregister (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?tableview=birthdays">Födelsedagslista</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/Mailaddresses.php">Mailadresser</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/MobileNumber.php">Mobilnummer (Hem utan mail)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/DossierReport.php" target="_blank">Godkännande personuppgifter (pdf-fil för alla i registret)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/users.php">Användare</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/AddressLabels.php?type=9x3" target="_empty">Adressetiketter 9x3 (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/AddressLabels.php?type=6x3" target="_empty">Adressetiketter 6x3 (pdf-fil)</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Statistik</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/views/statistics.php">Medlemsstatistik</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/charts.php">Medlemsstatistik grafik</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/efk.php">EFK-statistik</a></li>
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
            <!--            <li>
                            <a href="/<?php echo SARON_URI;?>dataexchange/extractDB.php">Extract</a>
                        </li>
                        <li> 
                            <a href="/<?php echo SARON_URI;?>dataexchange/insertDB.php">Insert</a>
                        </li>
            -->
                    </ul>
                        </div></td>
            </tr>
        </Table>
    </body>
</html>