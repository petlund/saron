<?php
header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once "config.php";
    require_once SARON_ROOT . "app/access/wp-authenticate.php";
    require_once SARON_ROOT . "menu.php";
    /*** REQUIRE USER AUTHENTICATION ***/
    isLoggedIn();
?>
<html>   
    <Head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Organisation</title> 
    </Head>
    <body> 
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/organization.unit.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/organization.structure.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/organization.role.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/organization.status.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/people.engagement.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/memberstate.js"></script>     
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/localization/jquery.jtable.se.js"></script>   
        <div class='saronSmallText'></div>
        <div id="<?php include('../includes/viewId.php');?>"></div>
    </body>
</html> 