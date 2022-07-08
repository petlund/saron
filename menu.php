<?php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: 0");

    require_once "config.php";
    require_once SARON_ROOT . "app/access/SaronCookie.php";
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . 'app/util/menyLink.php';
   
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
                <td style='text-align: left'><?php echo $saronUser->getDBConnectHeadLine();?>
                </td>
                <td>
                </td>
                <td style='text-align: right'><?php echo $saronUser->getLoginHeadLine();?>
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
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"people.php", TABLE_NAME_PEOPLE, "Personuppgifter");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"people.php", TABLE_NAME_BAPTIST, " - Dopuppgifter");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"people.php", TABLE_NAME_MEMBER, " - Medlemsuppgifter");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"people.php", TABLE_NAME_KEYS, " - Nyckelinnehav");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"homes.php",  TABLE_NAME_HOMES, "Hem");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"people.php", TABLE_NAME_TOTAL, "Registeröversikt");?></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Rapporter</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/DirectoryReport.php" target="_blank">Adresskalender (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>data/Organisationskalender.pdf" target="_blank">Organisationskalender beslutad (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/BaptistDirectoryReport.php" target="_blank">Dopregister (pdf-fil)</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"people.php", TABLE_NAME_BIRTHDAY, "Födelsedagslista");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"lists.php", LIST_EMAIL, "Mailadresser");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"lists.php", LIST_MOBILE_INSTEAD_OF_EMAIL, "Mobilnummer för saknade emailadresser");?></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/DossierReport.php" target="_blank">Godkännande personuppgifter (pdf-fil för alla i registret)</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"users.php", TABLE_NAME_USERS, "Användare");?></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/AddressLabels.php?type=9x3" target="_empty">Adressetiketter 9x3 (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/AddressLabels.php?type=6x3" target="_empty">Adressetiketter 6x3 (pdf-fil)</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/<?php echo SARON_URI;?>app/views/test.php">Medlemsstatistik</a>
                            <ul>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"statistics.php", TABLE_NAME_STATISTICS, "Medlemsstatistik");?></li>
                                <li><a href="/<?php echo SARON_URI;?>app/views/charts.php">Medlemsstatistik grafik</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"efk.php", TABLE_NAME_EFK, "EFK-statistik");?></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Organisation</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/OrganizationReport.php?type=proposal" target="_blank">Organisationskalender förslag(pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI;?>app/pdf/OrganizationReport.php?type=vacancy" target="_blank">Organisationskalender vakanser(pdf-fil)</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"engagement.php", TABLE_NAME_ENGAGEMENT, "Ansvar per person");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_UNITTREE, "Organisationsträd");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_UNITLIST, "Organisationslista");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_POS, "Positioner");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_ROLE, "Roller");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_UNITTYPE, "Organisatoriska enhetstype");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_ORG_ROLE_STATUS, "Bemanningsstatus");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_ORG_MEMBER_STATE, "Medlemsstatus");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_ORGVERSION, "Beslut om organisation");?></li>
                            <!-- 
                                <li><?php //echo getMenyLink(SARON_VIEW_URI,"organization.php", ORG_GRAPH, " --Grafisk presentation");?></li>
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
