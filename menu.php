<?php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: 0");

    require_once "config.php";
    require_once SARON_ROOT . "app/access/SaronCookie.php";
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . "app/database/ping.php";
   
?>  
<!doctype html>
<html>
  <head> 
    <meta charset="UTF-8">
<!--        <meta name="viewport" content="width=device-width, initial-scale=1.0">
-->
    <?php 
    require_once SARON_ROOT . "app/util/GlobalConstants_js.php";

    include ('app/util/js.php') ?>
    
    <link rel="icon" href=<?php echo Favicon;?> type="png"/>        
    <script><?php echo 'const saron = ' . $saronJSON.";";?></script> <!-- refer to app/util/GlobalConstants_js.php -->

    <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.js"></script>

    <link href="/<?php echo THREE_PP_URI;?>jtable/themes/lightcolor/gray/jtable.min.css" rel="stylesheet" type="text/css" />        
    <link href="/<?php echo THREE_PP_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet" type="text/css" />          

    <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("util/", "menu.js");?>"></script>     
    <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("util/", "timeout.js");?>"></script>     
    <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("util/", "util.js");?>"></script>     
    <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("util/", "tableNavigationUtil.js");?>"></script>     
    
    <link rel="stylesheet" type="text/css" href="/<?php echo SARON_URI;?>app/css/saron.css" />

    <?php 
        $db = new db();
        try{
            $saronUser = new SaronUser($db);
            $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE, TICKET_RENEWAL_CHECK);
        }
        catch(Exception $ex){
            header("Location: /" . SARON_URI . LOGOUT_URI);
            exit();                                                
        }
    ?>
    <body >
        <table  class='saronMenuTable saronSmallText'>
            <tr>
                <td style='text-align: left'><?php echo ping()?>
                </td>
                <td>
                </td>
                <td style='text-align: right'><?php include("app/util/userDisplay.php")?>
                </td>
                <td style='width: 20px'>
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
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?TableView=people">Personuppgifter</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?TableView=baptist"> - Dopuppgifter</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?TableView=member"> - Medlemsuppgifter</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?TableView=keys"> - Nyckelinnehav</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/homes.php?TableView=homes">Hem</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?TableView=total">Registeröversikt</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Rapporter</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/DirectoryReport.php" target="_blank">Adresskalender (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>data/Organisationskalender.pdf" target="_blank">Organisationskalender beslutad (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/BaptistDirectoryReport.php" target="_blank">Dopregister (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/people.php?TableView=birthdays">Födelsedagslista</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/lists.php?TableView=EMAIL_LIST">Mailadresser</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/lists.php?TableView=MOBILE_INSTEAD_OF_EMAIL">Mobilnummer (Hem utan mail)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/DossierReport.php" target="_blank">Godkännande personuppgifter (pdf-fil för alla i registret)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/users.php">Användare</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/AddressLabels.php?type=9x3" target="_empty">Adressetiketter 9x3 (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/AddressLabels.php?type=6x3" target="_empty">Adressetiketter 6x3 (pdf-fil)</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/<?php echo SARON_URI;?>app/views/test.php">Medlemsstatistik</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/views/statistics.php">Medlemsstatistik</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/charts.php">Medlemsstatistik grafik</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/efk.php">EFK-statistik</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Organisation</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/OrganizationReport.php?type=proposal" target="_blank">Organisationskalender förslag(pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/OrganizationReport.php?type=vacancy" target="_blank">Organisationskalender vakanser(pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/engagement.php?TableView=ORG_ENGAGEMENT">Ansvar per person</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_UNITTREE">Organisationsträd</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_UNITLIST">Organisationslista</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_POS">Positioner</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_ROLE">Roller</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_UNITTYPE">Organisatoriska enhetstyper</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_ROLE_STATUS">Bemanningsstatus</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=MEMBER_STATE">Medlemsstatus</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_VERSION">Beslut om organisation</a></li>
                            <!--    <li><a href="/<?php echo SARON_URI;?>app/views/organization.php?TableView=ORG_GRAPH">Grafisk presentation</a></li>
                            -->
                            </ul>
                        </li>
                        <li>
                            <a href="#">Om <?php echo NameOfRegistry;?></a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/about/about.php">Om <?php echo NameOfRegistry;?></a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/about/gdpr.php" target="_empty">Hantering av personuppgifter</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/about/help.php" target="_empty">Hjälp Medlemsfuntioner</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/about/organisation.php" target="_empty">Hjälp Organisationsfunktioner</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/<?php echo SARON_URI;?>app/access/SaronLogin.php?logout=true">Logga ut</a>
                        </li>
                    </ul>
                </div></td>
            </tr>
        </Table>
    </body>
</html>
