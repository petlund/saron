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
        <div class='saronSmallText'></div>
        <div id="#ORG_GRAPH" class="tree_graph"></div>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>vis4/dist/vis-network.min.js"></script>   
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/network/orgtree.js"></script> 
    </body>
</html> 