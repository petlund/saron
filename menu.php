<?php
    require_once "config.php";
    require_once SARON_ROOT . "app/access/SaronCookie.php";
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . 'app/util/menyLink.php';

    $db = new db();
    try{
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE, TICKET_RENEWAL_CHECK);
    }
    catch(Exception $ex){
        $db->saron_dev_log(LOG_ERR, "Menu", "hasValidSaronSession or new SaronUser", $ex);
        header("Location: /" . SARON_URI . LOGOUT_URI);
        exit();                                                
    }

    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: 0");

   
?>  
<!doctype html>
<html>
  <head> 
    <meta charset="UTF-8">

    <?php 
    require_once SARON_ROOT . "app/util/GlobalConstants_js.php";

    include ('app/util/distPath.php') ?>
    
    <link rel="icon" href=<?php echo Favicon;?> type="png"/>     
    <meta http-equiv="Content-Security-Policy" content="script-src <?php  $nonce = New Nonce($db, $saronUser); echo $nonce->getCSPNonce();?> 'self' https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js">
    <link rel="icon" href=<?php echo Favicon;?> type="png"/>        
    <script nonce=<?php echo $nonce->getScriptNonce();?>><?php echo 'const saron = ' . $saronJSON . ";";?></script>

    <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.js"></script>

    <link href="/<?php echo THREE_PP_URI;?>jtable/themes/lightcolor/gray/jtable.min.css" rel="stylesheet" type="text/css" />        
    <link href="/<?php echo THREE_PP_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet" type="text/css" />          

    <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_UTIL, "menu.js");?>"></script>     
    <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_UTIL, "timeout.js");?>"></script>     
    <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_UTIL, "util.js");?>"></script>     
    <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_UTIL, "formActions.js");?>"></script>     
    <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_UTIL, "tableNavigationUtil.js");?>"></script>     
  
    <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_UTIL, "security.js");?>"></script>     
    <link rel="stylesheet" type="text/css" href="/<?php echo getDistPath(APP_CSS, "saron.css");?>">     
    
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
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"changelog.php", TABLE_NAME_CHANGE, "Ändringslogg");?></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Rapporter</a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI . SARON_REPORTS_URI;?>DirectoryReport.php" target="_blank">Adresskalender (pdf-fil)</a></li>
                                <li><?php echo getMenyLink(SARON_DOC_URI,"index.php", "Organisationskalender", "Organisationskalender (pdf-fil)", true);?></li>
                                <li><a href="/<?php echo SARON_URI . SARON_REPORTS_URI;?>BaptistDirectoryReport.php" target="_blank">Dopregister (pdf-fil)</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"people.php", TABLE_NAME_BIRTHDAY, "Födelsedagslista");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"lists.php", LIST_EMAIL, "Mailadresser");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"lists.php", LIST_MOBILE_INSTEAD_OF_EMAIL, "Mobilnummer för saknade emailadresser");?></li>
                                <li><a href="/<?php echo SARON_URI . SARON_REPORTS_URI;?>DossierReport.php" target="_blank">Godkännande personuppgifter (pdf-fil för alla i registret)</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"users.php", TABLE_NAME_USERS, "Användare");?></li>
                                <li><a href="/<?php echo SARON_URI . SARON_REPORTS_URI;?>AddressLabels.php?type=9x3" target="_empty">Adressetiketter 9x3 (pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI . SARON_REPORTS_URI;?>AddressLabels.php?type=6x3" target="_empty">Adressetiketter 6x3 (pdf-fil)</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_MEMBER_STATE_REPORT, "Medlemsstatus och rapporter");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_MEMBER_STATE, "Medlemsstatus");?></li>
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
                                <li><a href="/<?php echo SARON_URI . SARON_REPORTS_URI;?>OrganizationReport.php?type=proposal" target="_blank">Organisationskalender förslag(pdf-fil)</a></li>
                                <li><a href="/<?php echo SARON_URI . SARON_REPORTS_URI;?>OrganizationReport.php?type=vacancy" target="_blank">Organisationskalender vakanser(pdf-fil)</a></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"engagement.php", TABLE_NAME_ENGAGEMENT, "Ansvar per person");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organizationSearch.php", TABLE_NAME_UNITTREE, "Organisationsträd");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organizationSearch.php", TABLE_NAME_UNITLIST, "Organisationslista");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organizationSearch.php", TABLE_NAME_POS, "Positioner");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organizationSearch.php", TABLE_NAME_ROLE, "Roller");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organizationSearch.php", TABLE_NAME_UNITTYPE, "Organisatoriska enhetstyper");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_ORG_ROLE_STATUS, "Bemanningsstatus");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"organization.php", TABLE_NAME_ORGVERSION, "Beslut om organisation");?></li>
                                <li><?php echo getMenyLink(SARON_VIEW_URI,"changelog.php", TABLE_NAME_CHANGE, "Ändringslogg");?></li>
                            <!-- 
                                <li><?php //echo getMenyLink(SARON_VIEW_URI,"organization.php", ORG_GRAPH, " --Grafisk presentation");?></li>
                            -->
                            </ul>
                        </li>
                        <li>
                            <a href="#">Om <?php echo NameOfRegistry;?></a>
                            <ul>
                                <li><a href="/<?php echo SARON_URI;?>app/about/index.php">Om Saron</a></li>
                                <li><?php echo getMenyLink(SARON_DOC_URI,"index.php", "common", "Allmänt om Saron", true);?></li>
                                <li><?php echo getMenyLink(SARON_DOC_URI,"index.php", "gdpr", "Hantering av personuppgifter", true);?></li>
                                <li><?php echo getMenyLink(SARON_DOC_URI,"index.php", "help", "Arbeta med personinformation", true);?></li>
                                <li><?php echo getMenyLink(SARON_DOC_URI,"index.php", "organisation", "Arbeta med organisationsinformation", true);?></li>
                                <li><?php echo getMenyLink(SARON_DOC_URI,"index.php", "account", "Uppsättning av konto", true);?></li>
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
